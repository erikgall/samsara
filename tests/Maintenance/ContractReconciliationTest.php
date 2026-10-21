<?php

namespace Samsara\Tests\Maintenance;

use PHPUnit\Framework\TestCase;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Process\Process;

/**
 * @author Erik Galloway <erik@erikgall.com>
 */
class ContractReconciliationTest extends TestCase
{
    private Filesystem $files;

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem;
        $this->root = sys_get_temp_dir().'/samsara-contract-'.bin2hex(random_bytes(8));
        $repository = dirname(__DIR__, 2);
        $this->files->copyDirectory($repository.'/src', $this->root.'/src');
        $this->files->copyDirectory($repository.'/config', $this->root.'/config');
        $this->files->makeDirectory($this->root.'/docs/api-sync', 0700, true);
        $this->files->copy($repository.'/docs/api-sync/contract-inventory.json', $this->inventoryPath());
        symlink($repository.'/contracts', $this->root.'/contracts');
        symlink($repository.'/.git', $this->root.'/.git');
    }

    protected function tearDown(): void
    {
        $this->files->deleteDirectory($this->root);

        parent::tearDown();
    }

    #[Test]
    public function it_keeps_explicit_generic_overrides_on_their_collection_paths(): void
    {
        $inventory = $this->files->json($this->inventoryPath());
        $resources = array_column($inventory['package_resources'], null, 'class');

        foreach (['AssetsResource' => '/assets', 'LiveSharingLinksResource' => '/live-shares'] as $class => $path) {
            $this->assertSame('DELETE', $resources[$class]['generic_bindings']['delete']['method']);
            $this->assertSame($path, $resources[$class]['generic_bindings']['delete']['path']);
            $this->assertSame('PATCH', $resources[$class]['generic_bindings']['update']['method']);
            $this->assertSame($path, $resources[$class]['generic_bindings']['update']['path']);
        }

        $this->assertSame(0, $this->check()->getExitCode());
    }

    #[Test]
    public function it_refreshes_source_evidence_and_writes_an_idempotent_inventory(): void
    {
        $path = $this->root.'/src/Resources/Fleet/DriversResource.php';
        $this->files->put($path, str_replace('<?php', "<?php\n", $this->files->get($path)));

        $this->assertSame(1, $this->check()->getExitCode());
        $this->assertSame(0, $this->check(['--write'])->getExitCode());
        $this->assertSame(0, $this->check()->getExitCode());
        $written = $this->files->get($this->inventoryPath());
        $this->assertSame(0, $this->check(['--write'])->getExitCode());
        $this->assertSame($written, $this->files->get($this->inventoryPath()));
    }

    #[Test]
    public function it_rejects_a_contract_other_than_the_pinned_dated_snapshot(): void
    {
        $before = $this->files->get($this->inventoryPath());
        $alternative = $this->root.'/alternative.json';
        $this->files->copy($this->root.'/contracts/samsara/samsara-api-2026-08-10.json', $alternative);

        $this->assertSame(1, $this->check(['--contract', $alternative, '--write'])->getExitCode());
        $this->assertSame($before, $this->files->get($this->inventoryPath()));
    }

    #[Test]
    public function it_rejects_a_fabricated_generic_item_path_without_writing_the_ledger(): void
    {
        $path = $this->root.'/src/Resources/Industrial/AssetsResource.php';
        $source = $this->files->get($path);
        $source = str_replace('    use CreatesEntities;', '    use CreatesEntities;'."\n".'    use FindsEntities;', $source);
        $source = str_replace('use Samsara\Resources\Concerns\CreatesEntities;', 'use Samsara\Resources\Concerns\CreatesEntities;'."\n".'use Samsara\Resources\Concerns\FindsEntities;', $source);
        $this->files->put($path, $source);
        $before = $this->files->get($this->inventoryPath());

        $this->assertSame(1, $this->check(['--write'])->getExitCode());
        $this->assertSame($before, $this->files->get($this->inventoryPath()));
    }

    #[Test]
    public function it_rejects_a_removed_resource_trait_even_with_a_current_source_digest(): void
    {
        $this->assertSame(0, $this->check()->getExitCode());
        $path = $this->root.'/src/Resources/Fleet/DriversResource.php';
        $original = $this->files->get($path);
        $originalInventory = $this->files->get($this->inventoryPath());
        $mutated = str_replace('    use FindsEntities;', '', $original, $count);
        $this->assertSame(1, $count);

        try {
            $this->files->put($path, $mutated);
            $this->refreshSourceDigest();
            $this->assertSame(1, $this->check()->getExitCode());
        } finally {
            $this->files->put($path, $original);
            $this->files->put($this->inventoryPath(), $originalInventory);
        }

        $this->assertSame($original, $this->files->get($path));
        $this->assertSame(0, $this->check()->getExitCode());
    }

    /** @param list<string> $arguments */
    private function check(array $arguments = []): Process
    {
        $process = new Process([
            PHP_BINARY,
            dirname(__DIR__, 2).'/scripts/check-samsara-contract-reconciliation.php',
            '--root', $this->root,
            ...$arguments,
        ]);
        $process->run();

        return $process;
    }

    private function inventoryPath(): string
    {
        return $this->root.'/docs/api-sync/contract-inventory.json';
    }

    private function refreshSourceDigest(): void
    {
        $paths = array_map(fn (\SplFileInfo $file): string => $file->getPathname(), $this->files->allFiles($this->root.'/src'));
        $paths[] = $this->root.'/config/samsara.php';
        sort($paths, SORT_STRING);
        $manifest = '';
        foreach ($paths as $path) {
            $relative = substr($path, strlen($this->root) + 1);
            $manifest .= hash('sha256', $this->files->get($path)).'  '.$relative."\n";
        }
        $inventory = $this->files->json($this->inventoryPath());
        $inventory['sources']['current_package_source']['source_sha256'] = hash('sha256', $manifest);
        $this->files->put($this->inventoryPath(), json_encode($inventory, JSON_THROW_ON_ERROR));
    }
}
