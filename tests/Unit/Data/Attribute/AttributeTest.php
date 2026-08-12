<?php

namespace Samsara\Tests\Unit\Data\Attribute;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Attribute\Attribute;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Attribute\AttributeValue;

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
            'entityType'    => 'asset',
            'attributeType' => 'text',
        ]);

        $this->assertSame('12345', $attribute->id);
        $this->assertSame('Department', $attribute->name);
        $this->assertSame('asset', $attribute->entityType);
        $this->assertSame('text', $attribute->attributeType);
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
    public function it_can_check_if_for_assets(): void
    {
        $attribute = new Attribute([
            'entityType' => 'asset',
        ]);

        $this->assertTrue($attribute->isForAssets());
        $this->assertFalse($attribute->isForDrivers());
    }

    #[Test]
    public function it_can_check_if_for_drivers(): void
    {
        $attribute = new Attribute([
            'entityType' => 'driver',
        ]);

        $this->assertTrue($attribute->isForDrivers());
        $this->assertFalse($attribute->isForAssets());
    }

    #[Test]
    public function it_can_check_if_number_type(): void
    {
        $attribute = new Attribute([
            'attributeType' => 'number',
        ]);

        $this->assertTrue($attribute->isNumberType());
        $this->assertFalse($attribute->isTextType());
    }

    #[Test]
    public function it_can_check_if_text_type(): void
    {
        $attribute = new Attribute([
            'attributeType' => 'text',
        ]);

        $this->assertTrue($attribute->isTextType());
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
                ['id' => 'val-1', 'stringValue' => 'Operations'],
                ['id' => 'val-2', 'stringValue' => 'Maintenance'],
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

    #[Test]
    public function it_returns_an_empty_collection_for_invalid_values(): void
    {
        $this->assertTrue((new Attribute)->getValues()->isEmpty());
        $this->assertTrue((new Attribute(['values' => null]))->getValues()->isEmpty());
        $this->assertTrue((new Attribute(['values' => 'invalid']))->getValues()->isEmpty());
    }

    #[Test]
    public function it_returns_values_as_a_typed_collection(): void
    {
        $attribute = new Attribute([
            'values' => [
                ['id' => 'val-1', 'stringValue' => 'Operations'],
                ['id' => 'val-2', 'stringValue' => 'Maintenance'],
            ],
        ]);

        $values = $attribute->getValues();

        $this->assertCount(2, $values);
        $this->assertInstanceOf(AttributeValue::class, $values->first());
        $this->assertSame('Operations', $values->first()->stringValue);
    }
}
