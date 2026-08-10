<?php

/**
 * Dependency-free behavior tests for the API snapshot validator.
 *
 * Run with: php tests/Maintenance/ApiSnapshotValidatorTest.php
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
final class ApiSnapshotValidatorTest
{
    private int $passed = 0;

    /** @var list<string> */
    private array $temporaryDirectories = [];

    public function run(): int
    {
        $tests = [
            'discovers and verifies the uniquely newest dated snapshot'   => fn () => $this->discoversNewestSnapshot(),
            'discovers snapshots in a directory with glob metacharacters' => fn () => $this->discoversSnapshotInLiteralDirectory(),
            'rejects a digest mismatch'                                   => fn () => $this->rejectsDigestMismatch(),
            'rejects newest-date ambiguity'                               => fn () => $this->rejectsNewestDateAmbiguity(),
            'rejects a missing dated snapshot'                            => fn () => $this->rejectsMissingSnapshot(),
            'rejects incomplete provenance metadata'                      => fn () => $this->rejectsIncompleteMetadata(),
            'rejects incomplete validator metadata'                       => fn () => $this->rejectsIncompleteValidatorMetadata(),
            'rejects calendar-invalid timestamps'                         => fn () => $this->rejectsInvalidTimestamp(),
            'rejects invalid timestamp offsets'                           => fn () => $this->rejectsInvalidTimestampOffset(),
            'rejects incomplete OpenAPI structure'                        => fn () => $this->rejectsIncompleteOpenApiStructure(),
            'rejects an invalid OpenAPI version marker'                   => fn () => $this->rejectsInvalidOpenApiVersion(),
            'rejects metadata that names a different path'                => fn () => $this->rejectsMismatchedMetadataPath(),
            'rejects symlinked evidence'                                  => fn () => $this->rejectsSymlinkedEvidence(),
            'rejects duplicate and conflicting options'                   => fn () => $this->rejectsInvalidOptionCombinations(),
        ];

        try {
            foreach ($tests as $name => $test) {
                $test();
                $this->passed++;
                fwrite(STDOUT, "PASS: {$name}\n");
            }
        } catch (Throwable $exception) {
            fwrite(STDERR, "FAIL: {$exception->getMessage()}\n");

            return 1;
        } finally {
            $this->cleanUp();
        }

        fwrite(STDOUT, "{$this->passed} maintenance validator tests passed.\n");

        return 0;
    }

    private function assertContains(string $needle, string $haystack): void
    {
        if (str_contains($haystack, $needle)) {
            return;
        }

        throw new RuntimeException("Expected output to contain: {$needle}\nActual output:\n{$haystack}");
    }

    private function assertSame(int $expected, int $actual, string $context = ''): void
    {
        if ($expected === $actual) {
            return;
        }

        throw new RuntimeException("Expected exit code {$expected}; got {$actual}. {$context}");
    }

    private function assertStringSame(string $expected, string $actual): void
    {
        if ($expected === $actual) {
            return;
        }

        throw new RuntimeException("Expected strings to be identical.\nExpected: {$expected}\nActual: {$actual}");
    }

    private function cleanUp(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            foreach (new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS) as $entry) {
                unlink($entry->getPathname());
            }

            rmdir($directory);
        }
    }

    private function discoversNewestSnapshot(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-09.json');
        $newestSnapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadata = $this->metadataPath($newestSnapshot);
        $snapshotDigestBefore = hash_file('sha256', $newestSnapshot);
        $metadataDigestBefore = hash_file('sha256', $metadata);

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(0, $result['exit_code'], $result['stderr']);
        $this->assertContains('Snapshot verified: '.realpath($newestSnapshot), $result['stdout']);
        $this->assertContains('OpenAPI version: 3.0.1', $result['stdout']);
        $this->assertStringSame($snapshotDigestBefore, hash_file('sha256', $newestSnapshot));
        $this->assertStringSame($metadataDigestBefore, hash_file('sha256', $metadata));
    }

    private function discoversSnapshotInLiteralDirectory(): void
    {
        $directory = $this->makeTemporaryDirectory('[literal]');
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(0, $result['exit_code'], $result['stderr']);
        $this->assertContains('Snapshot verified: '.realpath($snapshot), $result['stdout']);
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

    private function rejectsDigestMismatch(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata(
            $directory,
            'samsara-api-2026-08-10.json',
            str_repeat('0', 64),
        );

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('SHA-256 mismatch', $result['stderr']);
    }

    private function rejectsIncompleteMetadata(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshot($directory, 'samsara-api-2026-08-10.json');
        file_put_contents(
            $this->metadataPath($snapshot),
            json_encode([
                'snapshot' => basename($snapshot),
                'sha256'   => hash_file('sha256', $snapshot),
            ], JSON_THROW_ON_ERROR),
        );

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('Metadata field source_url must be a non-empty string', $result['stderr']);
    }

    private function rejectsIncompleteOpenApiStructure(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $directory.'/samsara-api-2026-08-10.json';
        file_put_contents($snapshot, json_encode(['openapi' => '3.0.1'], JSON_THROW_ON_ERROR));
        $this->writeMetadata($snapshot, hash_file('sha256', $snapshot));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('Snapshot field info must be an object', $result['stderr']);
    }

    private function rejectsIncompleteValidatorMetadata(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode(file_get_contents($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        unset($metadata['validator']['version']);
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('Metadata validation field version must be a non-empty string', $result['stderr']);
    }

    private function rejectsInvalidOpenApiVersion(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $snapshotDocument = json_decode(file_get_contents($snapshot), true, 512, JSON_THROW_ON_ERROR);
        $snapshotDocument['openapi'] = '2.0.0';
        file_put_contents($snapshot, json_encode($snapshotDocument, JSON_THROW_ON_ERROR));

        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode(file_get_contents($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        $metadata['sha256'] = hash_file('sha256', $snapshot);
        $metadata['raw_bytes'] = filesize($snapshot);
        $metadata['upstream']['openapi'] = '2.0.0';
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('Snapshot field openapi must be an OpenAPI 3 semantic version', $result['stderr']);
    }

    private function rejectsInvalidOptionCombinations(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');

        $duplicate = $this->runValidator(['--snapshot', '/missing.json', '--snapshot', $snapshot]);
        $conflicting = $this->runValidator(['--directory', '/missing', '--snapshot', $snapshot]);

        $this->assertSame(1, $duplicate['exit_code'], $duplicate['stderr']);
        $this->assertContains('Duplicate option: --snapshot', $duplicate['stderr']);
        $this->assertSame(1, $conflicting['exit_code'], $conflicting['stderr']);
        $this->assertContains('cannot be combined', $conflicting['stderr']);
    }

    private function rejectsInvalidTimestamp(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode(file_get_contents($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        $metadata['retrieved_at'] = '2026-02-30T12:00:00Z';
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('Metadata field retrieved_at is not a valid calendar timestamp', $result['stderr']);
    }

    private function rejectsInvalidTimestampOffset(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode(file_get_contents($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        $metadata['retrieved_at'] = '2026-08-10T12:00:00+24:00';
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('Metadata field retrieved_at is not a valid RFC 3339 offset', $result['stderr']);
    }

    private function rejectsMismatchedMetadataPath(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $snapshot = $this->writeSnapshotWithMetadata($directory, 'samsara-api-2026-08-10.json');
        $metadataPath = $this->metadataPath($snapshot);
        $metadata = json_decode(file_get_contents($metadataPath), true, 512, JSON_THROW_ON_ERROR);
        $metadata['snapshot'] = 'contracts/other/samsara-api-2026-08-10.json';
        file_put_contents($metadataPath, json_encode($metadata, JSON_THROW_ON_ERROR));

        $result = $this->runValidator(['--snapshot', $snapshot]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('must identify the validated snapshot path', $result['stderr']);
    }

    private function rejectsMissingSnapshot(): void
    {
        $directory = $this->makeTemporaryDirectory();

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('No dated JSON snapshots found', $result['stderr']);
    }

    private function rejectsNewestDateAmbiguity(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $this->writeSnapshotWithMetadata($directory, 'samsara-public-2026-08-10.json');
        $this->writeSnapshotWithMetadata($directory, 'samsara-private-2026-08-10.json');

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('Multiple snapshots have the newest date 2026-08-10', $result['stderr']);
    }

    private function rejectsSymlinkedEvidence(): void
    {
        $directory = $this->makeTemporaryDirectory();
        $target = $this->writeSnapshot($directory, 'snapshot-target.json');
        $snapshot = $directory.'/samsara-api-2026-08-10.json';
        symlink($target, $snapshot);
        $this->writeMetadata($snapshot, hash_file('sha256', $snapshot));

        $result = $this->runValidator(['--directory', $directory]);

        $this->assertSame(1, $result['exit_code'], $result['stderr']);
        $this->assertContains('must not be a symbolic link', $result['stderr']);
    }

    /**
     * @return array{exit_code: int, stdout: string, stderr: string}
     */
    private function runValidator(array $arguments): array
    {
        $repositoryRoot = dirname(__DIR__, 2);
        $command = array_merge(
            [PHP_BINARY, $repositoryRoot.'/scripts/validate-samsara-snapshot.php'],
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
        $this->writeMetadata($snapshot, $digest ?? hash_file('sha256', $snapshot));

        return $snapshot;
    }
}

exit((new ApiSnapshotValidatorTest)->run());
