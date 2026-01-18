<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Attribute;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Attribute\Attribute;

/**
 * Unit tests for the Attribute entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AttributeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $attribute = new Attribute([
            'id'            => '12345',
            'name'          => 'Department',
            'entityType'    => 'vehicle',
            'attributeType' => 'string',
        ]);

        $this->assertSame('12345', $attribute->id);
        $this->assertSame('Department', $attribute->name);
        $this->assertSame('vehicle', $attribute->entityType);
        $this->assertSame('string', $attribute->attributeType);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $attribute = Attribute::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertSame('12345', $attribute->getId());
    }

    #[Test]
    public function it_can_check_entity_type(): void
    {
        $attribute = new Attribute([
            'entityType' => 'vehicle',
        ]);

        $this->assertTrue($attribute->isForVehicles());
        $this->assertFalse($attribute->isForDrivers());
    }

    #[Test]
    public function it_can_check_if_for_drivers(): void
    {
        $attribute = new Attribute([
            'entityType' => 'driver',
        ]);

        $this->assertTrue($attribute->isForDrivers());
        $this->assertFalse($attribute->isForVehicles());
    }

    #[Test]
    public function it_can_check_if_number_type(): void
    {
        $attribute = new Attribute([
            'attributeType' => 'number',
        ]);

        $this->assertTrue($attribute->isNumberType());
        $this->assertFalse($attribute->isStringType());
    }

    #[Test]
    public function it_can_check_if_string_type(): void
    {
        $attribute = new Attribute([
            'attributeType' => 'string',
        ]);

        $this->assertTrue($attribute->isStringType());
        $this->assertFalse($attribute->isNumberType());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Department',
        ];

        $attribute = new Attribute($data);

        $this->assertSame($data, $attribute->toArray());
    }

    #[Test]
    public function it_can_have_values(): void
    {
        $attribute = new Attribute([
            'values' => [
                ['id' => 'val-1', 'value' => 'Operations'],
                ['id' => 'val-2', 'value' => 'Maintenance'],
            ],
        ]);

        $this->assertCount(2, $attribute->values);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $attribute = new Attribute;

        $this->assertInstanceOf(Entity::class, $attribute);
    }
}
