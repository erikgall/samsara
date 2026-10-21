<?php

namespace Samsara\Tests\Unit\Testing;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Testing\SamsaraFake;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\AuthenticationException;
use Illuminate\Http\Client\StrayRequestException;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Unit tests for the SamsaraFake class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraFakeTest extends TestCase
{
    #[Test]
    public function it_can_assert_nothing_requested(): void
    {
        $fake = SamsaraFake::create();

        $fake->assertNothingRequested();
    }

    #[Test]
    public function it_can_assert_requested(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']]);
        $fake->drivers()->query()->get();

        $fake->assertRequested('/fleet/drivers');
    }

    #[Test]
    public function it_can_assert_requested_with_params(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']]);
        $fake->drivers()->query()->where('tagIds', 'tag-1')->get();

        $fake->assertRequestedWithParams('/fleet/drivers', ['tagIds' => 'tag-1']);
    }

    #[Test]
    public function it_can_create_fake_instance(): void
    {
        $fake = SamsaraFake::create();

        $this->assertInstanceOf(SamsaraFake::class, $fake);
        $this->assertInstanceOf(Samsara::class, $fake);
    }

    #[Test]
    public function it_can_fake_drivers(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [
            ['id' => 'driver-1', 'name' => 'John Doe'],
        ]);

        $drivers = $fake->drivers()->query()->get();

        $this->assertCount(1, $drivers);
        $this->assertSame('John Doe', $drivers[0]->name);
    }

    #[Test]
    public function it_can_fake_empty_response(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', []);

        $drivers = $fake->drivers()->query()->get();

        $this->assertCount(0, $drivers);
    }

    #[Test]
    public function it_can_fake_response_for_endpoint(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [
            ['id' => 'driver-1', 'name' => 'John Doe'],
            ['id' => 'driver-2', 'name' => 'Jane Doe'],
        ]);

        $drivers = $fake->drivers()->query()->get();

        $this->assertCount(2, $drivers);
        $this->assertSame('driver-1', $drivers[0]->id);
    }

    #[Test]
    public function it_can_fake_response_with_status_code(): void
    {
        $fake = SamsaraFake::create();
        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']], 201);

        $this->assertSame(201, $fake->client()->get('/fleet/drivers')->status());
    }

    #[Test]
    public function it_can_fake_single_resource_response(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers/driver-123', [
            'id'   => 'driver-123',
            'name' => 'John Doe',
        ]);

        $driver = $fake->drivers()->find('driver-123');

        $this->assertSame('driver-123', $driver->id);
        $this->assertSame('John Doe', $driver->name);
    }

    #[Test]
    public function it_can_fake_vehicle_stats(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/vehicles/stats', [
            ['id' => 'vehicle-1', 'name' => 'Truck 1', 'gps' => ['latitude' => 37.4, 'longitude' => -122.0]],
        ]);

        $stats = $fake->vehicleStats()->current()->get();

        $this->assertCount(1, $stats);
    }

    #[Test]
    public function it_can_fake_vehicles(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/vehicles', [
            ['id' => 'vehicle-1', 'name' => 'Truck 1'],
        ]);

        $vehicles = $fake->vehicles()->query()->get();

        $this->assertCount(1, $vehicles);
        $this->assertSame('Truck 1', $vehicles[0]->name);
    }

    #[Test]
    public function it_does_not_assert_a_suffix_from_a_different_endpoint(): void
    {
        $fake = SamsaraFake::create();
        $fake->fakeResponse('/v1/fleet/assets', [['id' => 'asset-1']]);
        $fake->legacy()->fleetAssets([]);

        $this->expectException(ExpectationFailedException::class);

        $fake->assertRequested('/assets');
    }

    #[Test]
    public function it_does_not_assert_params_for_a_suffix_from_a_different_endpoint(): void
    {
        $fake = SamsaraFake::create();
        $fake->fakeResponse('/v1/fleet/assets', [['id' => 'asset-1']]);
        $fake->legacy()->fleetAssets(['groupId' => 123]);

        $this->expectException(ExpectationFailedException::class);

        $fake->assertRequestedWithParams('/assets', ['groupId' => 123]);
    }

    #[Test]
    public function it_does_not_match_a_suffix_from_a_different_endpoint(): void
    {
        $fake = SamsaraFake::create();
        $fake->fakeResponse('/assets', [['id' => 'asset-1']]);

        $this->expectException(StrayRequestException::class);

        $fake->legacy()->fleetAssets([]);
    }

    #[Test]
    public function it_fails_when_asserting_endpoint_not_requested(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']]);
        $fake->drivers()->query()->get();

        $this->expectException(ExpectationFailedException::class);

        $fake->assertRequested('/fleet/vehicles');
    }

    #[Test]
    public function it_fails_when_asserting_nothing_requested_but_has_requests(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']]);
        $fake->drivers()->query()->get();

        $this->expectException(ExpectationFailedException::class);

        $fake->assertNothingRequested();
    }

    #[Test]
    public function it_keeps_overlapping_endpoint_fakes_isolated(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/vehicles', [['id' => 'vehicle-1', 'name' => 'Vehicle list']]);
        $fake->fakeResponse('/fleet/vehicles/stats', [['id' => 'vehicle-1', 'name' => 'Vehicle stats']]);

        $vehicles = $fake->vehicles()->query()->get();
        $stats = $fake->vehicleStats()->current()->get();

        $this->assertSame('Vehicle list', $vehicles->first()->name);
        $this->assertSame('Vehicle stats', $stats->first()->name);
    }

    #[Test]
    public function it_no_longer_exposes_resource_specific_fake_helpers(): void
    {
        foreach (['fakeDrivers', 'fakeVehicles', 'fakeVehicleStats'] as $method) {
            $this->assertFalse(method_exists(SamsaraFake::class, $method));
        }
    }

    #[Test]
    public function it_preserves_recorded_requests_when_a_later_response_is_staged(): void
    {
        $fake = SamsaraFake::create();
        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']]);
        $fake->drivers()->query()->get();

        $fake->fakeResponse('/fleet/vehicles', [['id' => 'vehicle-1']]);

        $this->assertCount(1, $fake->getRecordedRequests());
    }

    #[Test]
    public function it_prevents_stray_requests_without_any_staged_response(): void
    {
        $fake = SamsaraFake::create();

        $this->expectException(StrayRequestException::class);

        $fake->drivers()->query()->get();
    }

    #[Test]
    public function it_records_requests(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']]);
        $fake->fakeResponse('/fleet/vehicles', [['id' => 'vehicle-1']]);

        $fake->drivers()->query()->get();
        $fake->vehicles()->query()->get();

        $requests = $fake->getRecordedRequests();

        $this->assertCount(2, $requests);
    }

    #[Test]
    public function it_rejects_a_requested_params_mismatch(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']]);
        $fake->drivers()->query()->where('tagIds', 'tag-1')->get();

        $fake->assertRequestedWithParams('/fleet/drivers', ['tagIds' => 'tag-1']);
        $this->expectException(ExpectationFailedException::class);

        $fake->assertRequestedWithParams('/fleet/drivers', ['tagIds' => 'tag-2']);
    }

    #[Test]
    public function it_rejects_unfaked_requests(): void
    {
        $fake = SamsaraFake::create();
        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']]);

        $this->expectException(StrayRequestException::class);

        $fake->vehicles()->query()->get();
    }

    #[Test]
    public function it_requires_an_exact_fake_for_a_driver_item_path(): void
    {
        $fake = SamsaraFake::create();
        $fake->fakeResponse('/fleet/drivers', [['id' => 'driver-1']]);

        $this->expectException(StrayRequestException::class);

        $fake->drivers()->find('driver-1');
    }

    #[Test]
    public function it_requires_an_exact_fake_for_vehicle_stats_history(): void
    {
        $fake = SamsaraFake::create();
        $fake->fakeResponse('/fleet/vehicles/stats', []);

        $this->expectException(StrayRequestException::class);

        $fake->vehicleStats()->history()->get();
    }

    #[Test]
    public function it_shares_staged_responses_and_history_across_token_and_region_clones(): void
    {
        $fake = SamsaraFake::create(['fleet/drivers' => [['id' => 'base-driver']]]);
        $fake->drivers()->query()->get();
        $carrier = $fake->withToken('carrier-token');
        $europe = $carrier->withRegion('eu');
        $carrier->fakeResponse('/fleet/drivers', [['id' => 'carrier-driver']]);

        $this->assertSame('carrier-driver', $europe->drivers()->query()->first()->id);
        $europe->fakeResponse('/fleet/drivers', [['id' => 'europe-driver']]);
        $this->assertSame('europe-driver', $carrier->drivers()->query()->first()->id);
        $this->assertSame('europe-driver', $fake->drivers()->query()->first()->id);
        $this->assertCount(4, $fake->getRecordedRequests());
        $this->assertSame($fake->getRecordedRequests(), $carrier->getRecordedRequests());
        $request = $fake->getRecordedRequests()[1];
        $this->assertSame(['Bearer carrier-token'], $request->header('Authorization'));
        $this->assertSame('api.eu.samsara.com', parse_url($request->url(), PHP_URL_HOST));
        $fake->assertRequested('/fleet/drivers');
    }

    #[Test]
    public function it_stages_responses_passed_to_create(): void
    {
        $fake = SamsaraFake::create([
            'fleet/drivers'   => [['id' => 'driver-1']],
            '/fleet/vehicles' => [['id' => 'vehicle-1']],
        ]);

        $this->assertSame('driver-1', $fake->drivers()->query()->first()->id);
        $this->assertSame('vehicle-1', $fake->vehicles()->query()->first()->id);
        $this->assertCount(2, $fake->getRecordedRequests());
    }

    #[Test]
    public function it_surfaces_a_staged_error_status(): void
    {
        $fake = SamsaraFake::create();
        $fake->fakeResponse('/fleet/drivers', [], 401);
        $this->expectException(AuthenticationException::class);

        $fake->drivers()->query()->get();
    }
}
