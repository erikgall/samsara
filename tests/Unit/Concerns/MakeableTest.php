<?php

namespace ErikGall\Samsara\Tests\Unit\Concerns;

use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Concerns\Makeable;

/**
 * Unit tests for the Makeable trait.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class MakeableTest extends TestCase
{
    #[Test]
    public function it_creates_instance_with_make(): void
    {
        $instance = MakeableTestClass::make(['name' => 'Test']);

        $this->assertInstanceOf(MakeableTestClass::class, $instance);
        $this->assertSame('Test', $instance->name);
    }

    #[Test]
    public function it_creates_instance_without_arguments(): void
    {
        $instance = MakeableTestClass::make();

        $this->assertInstanceOf(MakeableTestClass::class, $instance);
        $this->assertNull($instance->name);
    }
}

/**
 * Test class for Makeable trait.
 */
class MakeableTestClass
{
    use Makeable;

    public ?string $name = null;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->name = $attributes['name'] ?? null;
    }
}
