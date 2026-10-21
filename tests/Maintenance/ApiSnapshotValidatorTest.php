<?php

namespace Samsara\Tests\Maintenance;

use RuntimeException;
use DirectoryIterator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Behavior tests for repository maintenance validators.
 *
 * Run with: vendor/bin/phpunit tests/Maintenance
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
final class ApiSnapshotValidatorTest extends TestCase
{
    /** @var list<string> */
    private array $temporaryDirectories = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            foreach (new DirectoryIterator($directory) as $entry) {
                if (! $entry->isDot()) {
                    unlink($entry->getPathname());
                }
            }

            rmdir($directory);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_discovers_newest_snapshot(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-09.json', str_repeat('0', 64));
        $newestSnapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadata = $this->metadataPath($newestSnapshot);
        $snapshotDigestBefore = hash('sha256', $this->readFile($newestSnapshot));
        $metadataDigestBefore = hash('sha256', $this->readFile($metadata));

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(0, $result['exit_code'], $result['stderr']);
        $this->assertSame('', $result['stderr']);
        $this->assertSame($snapshotDigestBefore, hash('sha256', $this->readFile($newestSnapshot)));
        $this->assertSame($metadataDigestBefore, hash('sha256', $this->readFile($metadata)));
    }

    #[Test]
    public function it_discovers_snapshot_in_literal_directory(): void
    {
        $directory = $this->makeTemporaryDirectory('[literal]');
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(0, $result['exit_code'], $result['stderr']);
        $this->assertSame('', $result['stderr']);
    }

    #[Test]
    public function it_reconciles_contracts_from_linked_worktree(): void
    {
        $result = $this->runRepositoryScript('scripts/check-samsara-contract-reconciliation.php');

        $this->assertSame(0, $result['exit_code'], $result['stderr']);
        $this->assertSame('', $result['stderr']);
    }

    #[Test]
    public function it_rejects_digest_mismatch(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata(
            $directory,
            'samsara-api-2026-08-10.json',
            str_repeat('0', 64),
        );

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_incomplete_metadata(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadata = json_decode($this->readFile($this->metadataPath($snapshot)), true, flags: JSON_THROW_ON_ERROR);
        unset($metadata['source_url']);
        file_put_contents($this->metadataPath($snapshot), json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_incomplete_open_api_structure(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $document = json_decode($this->readFile($snapshot), true, flags: JSON_THROW_ON_ERROR);
        unset($document['info']);
        file_put_contents($snapshot, json_encode($document, JSON_THROW_ON_ERROR));
        $this->writeMetadata($snapshot, hash('sha256', $this->readFile($snapshot)));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_incomplete_validator_metadata(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode($this->readFile($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        unset($metadata['validator']['version']);
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_invalid_open_api_version(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $snapshotDocument = json_decode($this->readFile($snapshot), true, 512, JSON_THROW_ON_ERROR);
        $snapshotDocument['openapi'] = '2.0.0';
        file_put_contents($snapshot, json_encode($snapshotDocument, JSON_THROW_ON_ERROR));

        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode($this->readFile($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        $metadata['sha256'] = hash('sha256', $this->readFile($snapshot));
        $metadata['raw_bytes'] = filesize($snapshot);
        $metadata['upstream']['openapi'] = '2.0.0';
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_invalid_option_combinations(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');

        $duplicate = $this->runValidator(['--snapshot', $snapshot, '--snapshot', $snapshot]);
        $conflicting = $this->runValidator(['--directory', $directory, '--snapshot', $snapshot]);

        $this->assertSame(1, $duplicate['exit_code'], $duplicate['stderr']);
        $this->assertSame(1, $conflicting['exit_code'], $conflicting['stderr']);
    }

    #[Test]
    public function it_rejects_invalid_timestamp(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode($this->readFile($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        $metadata['retrieved_at'] = '2026-02-30T12:00:00Z';
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_invalid_timestamp_offset(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode($this->readFile($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        $metadata['retrieved_at'] = '2026-08-10T12:00:00+24:00';
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_mismatched_metadata_path(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode($this->readFile($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        $metadata['snapshot'] = 'contracts/other/samsara-api-2026-08-10.json';
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_missing_snapshot(): void
    {
        $directory = $this->makeTemporaryDirectory();

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_newest_date_ambiguity(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $this->writeSnapshotWithMetadata($directory, 'samsara-public-2026-08-10.json');
        $this->writeSnapshotWithMetadata($directory, 'samsara-private-2026-08-10.json');

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    #[Test]
    public function it_rejects_symlinked_evidence(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $target = $this->writeSnapshot($directory, 'snapshot-target.json');
        $snapshot = $directory.'/samsara-api-2026-08-10.json';
        symlink($target, $snapshot);
        $this->writeMetadata($snapshot, hash('sha256', $this->readFile($snapshot)));

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
    }

    private function makeTemporaryDirectory(string $suffix = ''): string
    {
        $directory = sys_get_temp_dir().'/samsara-snapshot-validator-'.bin2hex(random_bytes(8)).$suffix;

        if (! mkdir($directory, 0700)) {
            throw new RuntimeException('Could not create a temporary test directory.');
        }

        $this->temporaryDirectories[] = $directory;

        return $directory;
    }

    private function metadataPath(string $snapshot): string
    {
        return $snapshot.'.metadata.json';
    }

    private function readFile(string $path): string
    {
        $contents = file_get_contents($path);
        $this->assertIsString($contents);

        return $contents;
    }

    /**
     * @param  list<string>  $arguments
     * @return array{exit_code: int, stdout: string, stderr: string}
     */
    private function runRepositoryScript(string $script, array $arguments = []): array
    {
        $repositoryRoot = dirname(__DIR__, 2);
        $command = array_merge(
            [PHP_BINARY, $repositoryRoot.'/'.$script],
            $arguments,
        );
        $pipes = [];
        $process = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $repositoryRoot,
        );

        if (! is_resource($process)) {
            throw new RuntimeException('Could not start the validator process.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [
            'exit_code' => proc_close($process),
            'stdout'    => $stdout,
            'stderr'    => $stderr,
        ];
    }

    /**
     * @param  list<string>  $arguments
     * @return array{exit_code: int, stdout: string, stderr: string}
     */
    private function runValidator(array $arguments): array
    {
        return $this->runRepositoryScript('scripts/validate-samsara-snapshot.php', $arguments);
    }

    private function writeMetadata(string $snapshot, string $digest): void
    {
        file_put_contents($this->metadataPath($snapshot), json_encode([
            'snapshot'           => basename($snapshot),
            'sha256'             => $digest,
            'raw_bytes'          => filesize($snapshot),
            'source_url'         => 'https://developers.samsara.com/docs/openapi-spec',
            'retrieved_at'       => '2026-08-10T12:00:00-05:00',
            'retrieval_timezone' => 'America/Chicago',
            'http'               => [
                'status'        => 200,
                'date'          => 'Mon, 10 Aug 2026 17:00:00 GMT',
                'content_type'  => 'application/json',
                'etag'          => null,
                'last_modified' => null,
            ],
            'upstream' => [
                'openapi'      => '3.0.1',
                'info_version' => 'fixture',
                'commit'       => null,
            ],
            'authority' => [
                'guide_url'       => 'https://developers.samsara.com/docs/openapi-spec',
                'api_catalog_url' => 'https://developers.samsara.com/.well-known/api-catalog',
            ],
            'license' => [
                'declared_in_contract'         => null,
                'terms_of_service_in_contract' => null,
                'website_terms_url'            => 'https://www.samsara.com/legal/website-terms-of-service',
                'note'                         => 'Retained for package compatibility review; upstream terms still apply.',
            ],
            'validator' => [
                'name'      => 'fixture-validator',
                'version'   => '1.0.0',
                'command'   => 'fixture-validator snapshot.json',
                'result'    => 'valid',
                'exit_code' => 0,
            ],
        ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function writeSnapshot(string $directory, string $filename): string
    {
        $snapshot = $directory.'/'.$filename;
        file_put_contents($snapshot, json_encode([
            'openapi' => '3.0.1',
            'info'    => [
                'title'   => 'Samsara API',
                'version' => 'fixture',
            ],
            'paths' => [
                '/fleet/vehicles' => [],
            ],
        ], JSON_THROW_ON_ERROR));

        return $snapshot;
    }

    private function writeSnapshotWithMetadata(
        string $directory,
        string $filename,
        ?string $digest = null,
    ): string {
        $snapshot = $this->writeSnapshot($directory, $filename);
        $this->writeMetadata($snapshot, $digest ?? hash('sha256', $this->readFile($snapshot)));

        return $snapshot;
    }
}
