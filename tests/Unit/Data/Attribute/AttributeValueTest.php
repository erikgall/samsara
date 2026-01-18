<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Attribute;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Attribute\AttributeValue;

/**
 * Unit tests for the AttributeValue entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AttributeValueTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $attributeValue = new AttributeValue([
            'id'          => '12345',
            'attributeId' => 'attr-1',
            'value'       => 'Operations',
            'stringValue' => 'Operations',
            'numberValue' => null,
        ]);

        $this->assertSame('12345', $attributeValue->id);
        $this->assertSame('attr-1', $attributeValue->attributeId);
        $this->assertSame('Operations', $attributeValue->value);
        $this->assertSame('Operations', $attributeValue->stringValue);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $attributeValue = AttributeValue::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(AttributeValue::class, $attributeValue);
        $this->assertSame('12345', $attributeValue->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'    => '12345',
            'value' => 'Operations',
        ];

        $attributeValue = new AttributeValue($data);

        $this->assertSame($data, $attributeValue->toArray());
    }

    #[Test]
    public function it_can_get_value(): void
    {
        $attributeValue = new AttributeValue([
            'stringValue' => 'Test Value',
        ]);

        $this->assertSame('Test Value', $attributeValue->getValue());
    }

    #[Test]
    public function it_can_get_value_from_number(): void
    {
        $attributeValue = new AttributeValue([
            'numberValue' => 42,
        ]);

        $this->assertSame(42, $attributeValue->getValue());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $attributeValue = new AttributeValue;

        $this->assertInstanceOf(Entity::class, $attributeValue);
    }

    #[Test]
    public function it_prefers_string_value_over_number_value(): void
    {
        $attributeValue = new AttributeValue([
            'stringValue' => 'Text',
            'numberValue' => 100,
        ]);

        $this->assertSame('Text', $attributeValue->getValue());
    }

    #[Test]
    public function it_returns_null_when_no_value_set(): void
    {
        $attributeValue = new AttributeValue;

        $this->assertNull($attributeValue->getValue());
    }
}
