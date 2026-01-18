<?php

namespace Samsara\Tests\Unit\Data\Route;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Route\Route;
use Samsara\Data\Route\RouteStop;
use Samsara\Data\Route\RouteSettings;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Route entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class RouteTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $route = new Route([
            'id'     => '950e8400-e29b-41d4-a716-446655440005',
            'name'   => 'Route 1',
            'hubId'  => '550e8400-e29b-41d4-a716-446655440000',
            'planId' => '850e8400-e29b-41d4-a716-446655440003',
            'type'   => 'dynamic',
        ]);

        $this->assertSame('950e8400-e29b-41d4-a716-446655440005', $route->id);
        $this->assertSame('Route 1', $route->name);
        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $route->hubId);
        $this->assertSame('850e8400-e29b-41d4-a716-446655440003', $route->planId);
        $this->assertSame('dynamic', $route->type);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $route = Route::make([
            'id'   => '950e8400-e29b-41d4-a716-446655440005',
            'name' => 'Route 1',
        ]);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame('950e8400-e29b-41d4-a716-446655440005', $route->getId());
    }

    #[Test]
    public function it_can_check_if_edited(): void
    {
        $route = new Route([
            'isEdited' => true,
        ]);

        $this->assertTrue($route->isEdited());
    }

    #[Test]
    public function it_can_check_if_pinned(): void
    {
        $route = new Route([
            'isPinned' => true,
        ]);

        $this->assertTrue($route->isPinned());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '950e8400-e29b-41d4-a716-446655440005',
            'name' => 'Route 1',
            'type' => 'dynamic',
        ];

        $route = new Route($data);

        $this->assertSame($data, $route->toArray());
    }

    #[Test]
    public function it_can_get_display_name(): void
    {
        $route = new Route([
            'name' => 'Route 1',
        ]);

        $this->assertSame('Route 1', $route->getDisplayName());
    }

    #[Test]
    public function it_can_get_distance_in_kilometers(): void
    {
        $route = new Route([
            'distanceMeters' => 15000,
        ]);

        $this->assertSame(15.0, $route->getDistanceKilometers());
    }

    #[Test]
    public function it_can_get_distance_in_miles(): void
    {
        $route = new Route([
            'distanceMeters' => 16093,
        ]);

        $this->assertEqualsWithDelta(10.0, $route->getDistanceMiles(), 0.01);
    }

    #[Test]
    public function it_can_get_duration_in_hours(): void
    {
        $route = new Route([
            'durationSeconds' => 7200,
        ]);

        $this->assertSame(2.0, $route->getDurationHours());
    }

    #[Test]
    public function it_can_get_duration_in_minutes(): void
    {
        $route = new Route([
            'durationSeconds' => 3600,
        ]);

        $this->assertSame(60.0, $route->getDurationMinutes());
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
        $this->assertSame('arriveFirstStop', $settings->routeStartingCondition);
        $this->assertSame('manual', $settings->sequencingMethod);
    }

    #[Test]
    public function it_can_get_stop_count(): void
    {
        $route = new Route([
            'stops' => [
                ['id' => 'stop-1'],
                ['id' => 'stop-2'],
                ['id' => 'stop-3'],
            ],
        ]);

        $this->assertSame(3, $route->getStopCount());
    }

    #[Test]
    public function it_can_get_stops_as_entities(): void
    {
        $route = new Route([
            'stops' => [
                ['id' => 'stop-1', 'name' => 'Stop 1'],
                ['id' => 'stop-2', 'name' => 'Stop 2'],
            ],
        ]);

        $stops = $route->getStops();

        $this->assertCount(2, $stops);
        $this->assertInstanceOf(RouteStop::class, $stops[0]);
        $this->assertInstanceOf(RouteStop::class, $stops[1]);
        $this->assertSame('stop-1', $stops[0]->id);
        $this->assertSame('Stop 2', $stops[1]->name);
    }

    #[Test]
    public function it_can_have_cost(): void
    {
        $route = new Route([
            'cost' => 125.5,
        ]);

        $this->assertSame(125.5, $route->cost);
    }

    #[Test]
    public function it_can_have_dispatch_route_id(): void
    {
        $route = new Route([
            'dispatchRouteId' => '123456',
        ]);

        $this->assertSame('123456', $route->dispatchRouteId);
    }

    #[Test]
    public function it_can_have_distance_and_duration(): void
    {
        $route = new Route([
            'distanceMeters'  => 15420,
            'durationSeconds' => 3600,
        ]);

        $this->assertSame(15420, $route->distanceMeters);
        $this->assertSame(3600, $route->durationSeconds);
    }

    #[Test]
    public function it_can_have_quantities(): void
    {
        $route = new Route([
            'quantities' => [
                ['name' => 'Weight', 'value' => 500],
                ['name' => 'Volume', 'value' => 100],
            ],
        ]);

        $this->assertCount(2, $route->quantities);
    }

    #[Test]
    public function it_can_have_scheduled_times(): void
    {
        $route = new Route([
            'scheduledRouteStartTime' => '2024-04-10T08:00:00Z',
            'scheduledRouteEndTime'   => '2024-04-10T17:00:00Z',
        ]);

        $this->assertSame('2024-04-10T08:00:00Z', $route->scheduledRouteStartTime);
        $this->assertSame('2024-04-10T17:00:00Z', $route->scheduledRouteEndTime);
    }

    #[Test]
    public function it_can_have_settings(): void
    {
        $route = new Route([
            'settings' => [
                'routeCompletionCondition' => 'departLastStop',
                'routeStartingCondition'   => 'arriveFirstStop',
            ],
        ]);

        $this->assertIsArray($route->settings);
    }

    #[Test]
    public function it_can_have_stops(): void
    {
        $route = new Route([
            'stops' => [
                ['id' => 'stop-1', 'name' => 'Stop 1'],
                ['id' => 'stop-2', 'name' => 'Stop 2'],
            ],
        ]);

        $this->assertCount(2, $route->stops);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $route = new Route([
            'createdAt' => '2024-04-10T10:30:00Z',
            'updatedAt' => '2024-04-10T11:00:00Z',
        ]);

        $this->assertSame('2024-04-10T10:30:00Z', $route->createdAt);
        $this->assertSame('2024-04-10T11:00:00Z', $route->updatedAt);
    }

    #[Test]
    public function it_can_have_timezone(): void
    {
        $route = new Route([
            'orgLocationTimezone' => 'America/Los_Angeles',
        ]);

        $this->assertSame('America/Los_Angeles', $route->orgLocationTimezone);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $route = new Route;

        $this->assertInstanceOf(Entity::class, $route);
    }

    #[Test]
    public function it_returns_empty_array_when_no_stops(): void
    {
        $route = new Route;

        $this->assertSame([], $route->getStops());
    }

    #[Test]
    public function it_returns_false_for_edited_when_not_set(): void
    {
        $route = new Route;

        $this->assertFalse($route->isEdited());
    }

    #[Test]
    public function it_returns_false_for_pinned_when_not_set(): void
    {
        $route = new Route;

        $this->assertFalse($route->isPinned());
    }

    #[Test]
    public function it_returns_false_when_not_edited(): void
    {
        $route = new Route([
            'isEdited' => false,
        ]);

        $this->assertFalse($route->isEdited());
    }

    #[Test]
    public function it_returns_false_when_not_pinned(): void
    {
        $route = new Route([
            'isPinned' => false,
        ]);

        $this->assertFalse($route->isPinned());
    }

    #[Test]
    public function it_returns_null_distance_when_not_set(): void
    {
        $route = new Route;

        $this->assertNull($route->getDistanceKilometers());
        $this->assertNull($route->getDistanceMiles());
    }

    #[Test]
    public function it_returns_null_duration_when_not_set(): void
    {
        $route = new Route;

        $this->assertNull($route->getDurationMinutes());
        $this->assertNull($route->getDurationHours());
    }

    #[Test]
    public function it_returns_null_when_no_settings(): void
    {
        $route = new Route;

        $this->assertNull($route->getSettings());
    }

    #[Test]
    public function it_returns_unknown_for_missing_display_name(): void
    {
        $route = new Route;

        $this->assertSame('Unknown', $route->getDisplayName());
    }

    #[Test]
    public function it_returns_zero_stop_count_when_no_stops(): void
    {
        $route = new Route;

        $this->assertSame(0, $route->getStopCount());
    }
}
