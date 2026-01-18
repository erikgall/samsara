<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Vehicle;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Vehicle\Gateway;

/**
 * Unit tests for the Gateway entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class GatewayTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $gateway = new Gateway([
            'serial' => 'ABCD-123-XYZ',
            'model'  => 'VG34',
        ]);

        $this->assertSame('ABCD-123-XYZ', $gateway->serial);
        $this->assertSame('VG34', $gateway->model);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $gateway = Gateway::make([
            'serial' => 'ABCD-123-XYZ',
        ]);

        $this->assertInstanceOf(Gateway::class, $gateway);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'serial' => 'ABCD-123-XYZ',
            'model'  => 'VG34',
        ];

        $gateway = new Gateway($data);

        $this->assertSame($data, $gateway->toArray());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $gateway = new Gateway;

        $this->assertInstanceOf(Entity::class, $gateway);
    }
}
