<?php

namespace Samsara\Tests\Unit\Data\Attribute;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Attribute\AttributeValue;

/**
 * Unit tests for the AttributeValue entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AttributeValueTest extends TestCase
{
    #[Test]
    public function it_can_get_value(): void
    {
        $attributeValue = new AttributeValue([
            'stringValue' => 'Test Value',
        ]);

        $this->assertSame('Test Value', $attributeValue->getValue());
    }

    #[Test]
    public function it_legacy_numeric_values_do_not_override_the_contract_string_value(): void
    {
        $attributeValue = new AttributeValue([
            'stringValue' => 'Text',
            'numberValue' => 100,
        ]);

        $this->assertSame('Text', $attributeValue->getValue());
    }

    #[Test]
    public function it_legacy_numeric_values_remain_backwards_compatible(): void
    {
        $attributeValue = new AttributeValue([
            'numberValue' => 42,
        ]);

        $this->assertSame(42, $attributeValue->getValue());
    }

    #[Test]
    public function it_returns_null_when_no_value_set(): void
    {
        $attributeValue = new AttributeValue;

        $this->assertNull($attributeValue->getValue());
    }
}
