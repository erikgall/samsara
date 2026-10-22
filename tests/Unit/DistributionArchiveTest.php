<?php

namespace Samsara\Tests\Unit;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Tests the package distribution exclusion contract.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DistributionArchiveTest extends TestCase
{
    /**
     * @var array<string>
     */
    private const EXCLUDED_PATHS = [
        '/contracts',
        '/docs',
        '/node_modules',
        '/scripts',
        '/tests',
        '/vendor',
        '/.ai',
        '/.claude',
        '/.DS_Store',
        '/.env*',
        '/.phpunit.cache',
        '/.phpunit.result.cache',
        '/auth.json',
        '/notes',
        '/.github',
        '/.prettierrc',
        '/CLAUDE.md',
        '/DEVELOPMENT_CHANGELOG.md',
        '/composer.lock',
        '/package-lock.json',
        '/package.json',
        '/phpstan.neon',
        '/phpunit.xml',
        '/pint.json',
        '/samsara.json',
    ];

    #[Test]
    public function it_excludes_repository_only_paths_from_git_and_composer_archives(): void
    {
        $root = dirname(__DIR__, 2);
        $attributes = (string) file_get_contents($root.'/.gitattributes');
        $composer = json_decode(
            (string) file_get_contents($root.'/composer.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $composerExcludes = $composer['archive']['exclude'] ?? [];

        foreach (self::EXCLUDED_PATHS as $path) {
            $this->assertStringContainsString("{$path} export-ignore", $attributes, $path);
            $this->assertContains($path, $composerExcludes, $path);
        }
    }

    #[Test]
    public function it_links_shipped_documentation_to_guides_available_outside_the_archive(): void
    {
        $root = dirname(__DIR__, 2);

        foreach (['README.md', 'UPGRADE.md', 'BREAKING_CHANGES.md'] as $path) {
            $contents = (string) file_get_contents($root.'/'.$path);

            $this->assertDoesNotMatchRegularExpression('/\]\((?:\.\/)?docs\//', $contents, $path);
        }

        $this->assertStringContainsString(
            'https://github.com/erikgall/samsara/blob/main/docs/upgrade-guide-v2.md',
            (string) file_get_contents($root.'/UPGRADE.md'),
        );
    }
}
