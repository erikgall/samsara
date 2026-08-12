<?php

namespace Samsara\Tests\Unit\Data\Route;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Route\Route;
use Samsara\Data\Route\RouteStop;
use Samsara\Data\Route\RouteSettings;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the current Route response entity.
 */
class RouteTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $route = Route::make(['id' => 'route-1', 'name' => 'Current Route']);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame('route-1', $route->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'               => 'route-1',
            'name'             => 'Current Route',
            'orgLocalTimezone' => 'America/Chicago',
        ];

        $this->assertSame($data, (new Route($data))->toArray());
    }

    #[Test]
    public function it_can_get_settings_as_entity(): void
    {
        $route = new Route([
            'settings' => [
                'routeCompletionCondition' => 'departLastStop',
                'routeStartingCondition'   => 'arriveFirstStop',
                'sequencingMethod'         => 'manual',
            ],
        ]);

        $settings = $route->getSettings();

        $this->assertInstanceOf(RouteSettings::class, $settings);
        $this->assertSame('departLastStop', $settings->routeCompletionCondition);
    }

    #[Test]
    public function it_can_get_stops_as_entities(): void
    {
        $route = new Route([
            'stops' => [
                ['id' => 'stop-1', 'name' => 'Stop 1', 'state' => 'scheduled', 'orders' => []],
                ['id' => 'stop-2', 'name' => 'Stop 2', 'state' => 'arrived', 'orders' => []],
            ],
        ]);

        $stops = $route->getStops();

        $this->assertSame(2, $route->getStopCount());
        $this->assertCount(2, $stops);
        $this->assertInstanceOf(RouteStop::class, $stops[0]);
        $this->assertSame('arrived', $stops[1]->state);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $this->assertInstanceOf(Entity::class, new Route);
    }

    #[Test]
    public function it_gets_an_exact_string_external_id(): void
    {
        $route = new Route([
            'externalIds' => [
                'customer' => 'route-123',
                'empty'    => '',
            ],
        ]);

        $this->assertSame('route-123', $route->getExternalId('customer'));
        $this->assertSame('', $route->getExternalId('empty'));
        $this->assertNull($route->getExternalId('missing'));
        $this->assertNull((new Route)->getExternalId('customer'));
        $this->assertNull((new Route(['externalIds' => null]))->getExternalId('customer'));
    }

    #[Test]
    public function it_returns_current_route_fields_without_obsolete_plan_helpers(): void
    {
        $route = new Route([
            'id'                   => 'route-1',
            'name'                 => 'Current Route',
            'orgLocalTimezone'     => 'America/Chicago',
            'actualRouteStartTime' => '2026-08-10T14:00:00Z',
            'actualRouteEndTime'   => '2026-08-10T15:00:00Z',
            'driver'               => ['id' => 'driver-1', 'name' => 'Ada Driver'],
            'vehicle'              => ['id' => 'vehicle-1', 'name' => 'Truck 1'],
            'tagIds'               => ['tag-1'],
        ]);

        $this->assertSame('Current Route', $route->getDisplayName());
        $this->assertSame('America/Chicago', $route->orgLocalTimezone);
        $this->assertSame('driver-1', $route->driver['id']);
        $this->assertSame('vehicle-1', $route->vehicle['id']);
        $this->assertFalse(method_exists($route, 'getDistanceMiles'));
        $this->assertFalse(method_exists($route, 'getDurationMinutes'));
        $this->assertFalse(method_exists($route, 'isEdited'));
        $this->assertFalse(method_exists($route, 'isPinned'));
    }

    #[Test]
    public function it_returns_empty_nested_values_when_not_set(): void
    {
        $route = new Route;

        $this->assertSame('Unknown', $route->getDisplayName());
        $this->assertSame(0, $route->getStopCount());
        $this->assertSame([], $route->getStops());
        $this->assertNull($route->getSettings());
    }
}
