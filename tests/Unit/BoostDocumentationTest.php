<?php

namespace Samsara\Tests\Unit;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Laravel Boost package publication contract tests.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class BoostDocumentationTest extends TestCase
{
    private const EXPECTED_SKILLS = [
        'samsara-sdk-development',
        'upgrade-samsara-sdk-v2',
    ];

    #[Test]
    public function it_documents_boost_discovery_and_keeps_local_and_published_skills_distinct(): void
    {
        $readme = $this->readProjectFile('README.md');
        $claude = $this->readProjectFile('CLAUDE.md');

        $this->assertStringContainsString('php artisan boost:update --discover', $readme);
        $this->assertStringContainsString('resources/boost/guidelines/', $readme);
        $this->assertStringContainsString('resources/boost/skills/', $readme);
        $this->assertStringContainsString('samsara-sdk-maintenance', $claude);
        $this->assertFileExists($this->projectPath('.ai/skills/samsara-sdk-maintenance/SKILL.md'));
        $this->assertFileDoesNotExist($this->projectPath('.ai/skills/samsara-sdk-development/SKILL.md'));
    }

    #[Test]
    public function it_keeps_the_post_release_checklist_current_and_scope_safe(): void
    {
        $checklist = $this->readProjectFile('.ai/skills/post-release-docs-checklist/SKILL.md');

        $this->assertStringContainsString('WebhookEvent::cases()', $checklist);
        $this->assertStringContainsString('does not broaden the release scope', $checklist);
        $this->assertDoesNotMatchRegularExpression('/currently \d+/', $checklist);
        $this->assertStringNotContainsString('Use 4-5 parallel subagents', $checklist);
    }

    #[Test]
    public function it_publishes_a_concise_guideline_that_routes_to_skills(): void
    {
        $guidelineFiles = glob($this->projectPath('resources/boost/guidelines/*')) ?: [];
        $guideline = $this->readProjectFile('resources/boost/guidelines/core.blade.php');

        $this->assertSame(
            [$this->projectPath('resources/boost/guidelines/core.blade.php')],
            array_values($guidelineFiles),
        );
        $this->assertLessThanOrEqual(130, str_word_count(strip_tags($guideline)));
        $this->assertStringContainsString('samsara-sdk-development', $guideline);
        $this->assertStringContainsString('upgrade-samsara-sdk-v2', $guideline);
        $this->assertStringContainsString('UnsupportedOperationException', $guideline);
        $this->assertStringNotContainsString('```', $guideline);
    }

    #[Test]
    public function it_publishes_only_valid_expected_agent_skills(): void
    {
        $skillsRoot = $this->projectPath('resources/boost/skills');
        $skillDirectories = array_values(array_filter(
            glob($skillsRoot.'/*') ?: [],
            'is_dir',
        ));
        $skillNames = array_map('basename', $skillDirectories);
        sort($skillNames);

        $expected = self::EXPECTED_SKILLS;
        sort($expected);

        $this->assertSame($expected, $skillNames);

        foreach ($skillDirectories as $skillDirectory) {
            $this->assertValidSkill($skillDirectory);
        }
    }

    private function assertValidSkill(string $skillDirectory): void
    {
        $skillFile = $skillDirectory.'/SKILL.md';
        $this->assertFileExists($skillFile);

        $contents = file_get_contents($skillFile);
        $this->assertIsString($contents);
        $this->assertMatchesRegularExpression('/\A---\n(?<frontmatter>.*?)\n---\n(?<body>.*)\z/s', $contents);

        preg_match('/\A---\n(?<frontmatter>.*?)\n---\n(?<body>.*)\z/s', $contents, $matches);
        $metadata = $this->parseSimpleFrontmatter($matches['frontmatter']);

        $this->assertSame(['description', 'name'], array_keys($metadata));
        $this->assertSame(basename($skillDirectory), $metadata['name']);
        $this->assertMatchesRegularExpression('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $metadata['name']);
        $this->assertLessThanOrEqual(64, strlen($metadata['name']));
        $this->assertNotSame('', $metadata['description']);
        $this->assertLessThanOrEqual(1024, strlen($metadata['description']));
        $this->assertLessThan(500, substr_count($matches['body'], "\n") + 1);
        $this->assertStringNotContainsString('## When to Use', $matches['body']);
        $this->assertStringNotContainsString('## When to use', $matches['body']);

        preg_match_all('/\[[^\]]+\]\((?<path>[^)]+\.md)\)/', $matches['body'], $links);

        foreach ($links['path'] as $relativePath) {
            $target = realpath($skillDirectory.'/'.$relativePath);

            $this->assertNotFalse($target, "Missing skill reference: {$relativePath}");
            $this->assertStringStartsWith(realpath($skillDirectory).DIRECTORY_SEPARATOR, $target);
        }
    }

    /**
     * Parse the deliberately restricted name/description frontmatter used by published skills.
     *
     * @return array{description: string, name: string}
     */
    private function parseSimpleFrontmatter(string $frontmatter): array
    {
        $metadata = [];

        foreach (explode("\n", $frontmatter) as $line) {
            $parts = explode(':', $line, 2);
            $this->assertCount(2, $parts, "Invalid frontmatter line: {$line}");

            $key = trim($parts[0]);
            $value = trim($parts[1]);

            $this->assertContains($key, ['name', 'description']);
            $this->assertArrayNotHasKey($key, $metadata);
            $metadata[$key] = trim($value, "\"'");
        }

        ksort($metadata);

        $this->assertSame(['description', 'name'], array_keys($metadata));

        /** @var array{description: string, name: string} $metadata */
        return $metadata;
    }

    private function projectPath(string $path): string
    {
        return dirname(__DIR__, 2).'/'.$path;
    }

    private function readProjectFile(string $path): string
    {
        $contents = file_get_contents($this->projectPath($path));
        $this->assertIsString($contents);

        return $contents;
    }
}
