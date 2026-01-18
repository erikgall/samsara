<?php

namespace Samsara\Tests\Unit\Data\Driver;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Driver\EldSettings;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the EldSettings entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EldSettingsTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $settings = EldSettings::make([
            'rulesets' => [],
        ]);

        $this->assertInstanceOf(EldSettings::class, $settings);
    }

    #[Test]
    public function it_can_be_created_with_rulesets(): void
    {
        $settings = new EldSettings([
            'rulesets' => [
                ['name' => 'US Property', 'cycle' => '70_8'],
            ],
        ]);

        $this->assertIsArray($settings->rulesets);
        $this->assertCount(1, $settings->rulesets);
    }

    #[Test]
    public function it_can_check_if_has_rulesets(): void
    {
        $settings = new EldSettings([
            'rulesets' => [
                ['name' => 'US Property'],
            ],
        ]);

        $this->assertTrue($settings->hasRulesets());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'rulesets' => [
                ['name' => 'US Property', 'cycle' => '70_8'],
            ],
        ];

        $settings = new EldSettings($data);

        $this->assertSame($data, $settings->toArray());
    }

    #[Test]
    public function it_can_get_rulesets(): void
    {
        $rulesets = [
            ['name' => 'US Property', 'cycle' => '70_8'],
            ['name' => 'California Property', 'cycle' => '80_8'],
        ];

        $settings = new EldSettings([
            'rulesets' => $rulesets,
        ]);

        $this->assertSame($rulesets, $settings->getRulesets());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $settings = new EldSettings;

        $this->assertInstanceOf(Entity::class, $settings);
    }

    #[Test]
    public function it_returns_empty_array_when_no_rulesets(): void
    {
        $settings = new EldSettings;

        $this->assertSame([], $settings->getRulesets());
    }

    #[Test]
    public function it_returns_false_for_has_rulesets_when_empty(): void
    {
        $settings = new EldSettings([
            'rulesets' => [],
        ]);

        $this->assertFalse($settings->hasRulesets());
    }
}
