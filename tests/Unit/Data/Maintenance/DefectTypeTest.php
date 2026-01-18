<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Maintenance;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Maintenance\DefectType;

/**
 * Unit tests for the DefectType entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DefectTypeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $defectType = new DefectType([
            'id'   => '12345',
            'name' => 'Air Compressor',
        ]);

        $this->assertSame('12345', $defectType->id);
        $this->assertSame('Air Compressor', $defectType->name);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $defectType = DefectType::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(DefectType::class, $defectType);
        $this->assertSame('12345', $defectType->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Air Compressor',
        ];

        $defectType = new DefectType($data);

        $this->assertSame($data, $defectType->toArray());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $defectType = new DefectType;

        $this->assertInstanceOf(Entity::class, $defectType);
    }
}
