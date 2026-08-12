<?php

namespace Samsara\Tests\Unit\Data\Vehicle;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Vehicle\Gateway;
use PHPUnit\Framework\Attributes\Test;

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
    public function it_exposes_current_gateway_detail_fields(): void
    {
        $gateway = new Gateway([
            'serial'              => 'ABCD-123-XYZ',
            'model'               => 'VG34',
            'accessoryDevices'    => [['serial' => 'ACC-1']],
            'asset'               => ['id' => 'vehicle-1', 'name' => 'Truck 1'],
            'connectionStatus'    => ['healthStatus' => 'Connected'],
            'dataUsageLast30Days' => ['bytesUsed' => 1024],
        ]);

        $this->assertCount(1, $gateway->accessoryDevices);
        $this->assertSame('vehicle-1', $gateway->asset['id']);
        $this->assertSame('Connected', $gateway->connectionStatus['healthStatus']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $gateway = new Gateway;

        $this->assertInstanceOf(Entity::class, $gateway);
    }

    #[Test]
    public function it_only_reports_the_documented_connected_health_status(): void
    {
        $this->assertTrue((new Gateway([
            'connectionStatus' => ['healthStatus' => 'Connected'],
        ]))->isConnected());

        foreach ([
            new Gateway,
            new Gateway(['connectionStatus' => null]),
            new Gateway(['connectionStatus' => ['healthStatus' => 'Recently Offline']]),
            new Gateway(['connectionStatus' => ['status' => 'Connected']]),
        ] as $gateway) {
            $this->assertFalse($gateway->isConnected());
        }
    }
}
