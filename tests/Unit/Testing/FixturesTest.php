<?php

namespace Samsara\Tests\Unit\Testing;

use RuntimeException;
use Samsara\Tests\TestCase;
use Samsara\Testing\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the Fixtures class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class FixturesTest extends TestCase
{
    /** @var array<string, string> */
    private const FIXTURE_SCHEMAS = [
        'addresses.json'     => 'Address',
        'drivers.json'       => 'Driver',
        'dvirs.json'         => 'DvirStreamResponseDataResponseBody',
        'equipment.json'     => 'Equipment',
        'hos-logs.json'      => 'HosLogsForDriver',
        'routes.json'        => 'BaseRouteWithOrdersResponseObjectResponseBody',
        'safety-events.json' => 'SafetyEvent',
        'tags.json'          => 'Tag',
        'trailers.json'      => 'TrailerResponseObjectResponseBody',
        'users.json'         => 'User',
        'vehicle-stats.json' => 'VehicleStatsResponse_data',
        'vehicles.json'      => 'VehicleResponseObjectResponseBody',
        'webhooks.json'      => 'WebhookResponseResponseBody',
    ];

    #[Test]
    public function it_can_load_addresses_fixture(): void
    {
        $addresses = Fixtures::addresses();

        $this->assertArrayHasKey('data', $addresses);
        $this->assertIsArray($addresses['data']);
        $this->assertNotEmpty($addresses['data']);
        $this->assertArrayHasKey('formattedAddress', $addresses['data'][0]);
        $this->assertIsString($addresses['data'][0]['tags'][0]['id']);
        $this->assertArrayDoesNotHaveKeys(['tagIds'], $addresses['data'][0]);
        $this->assertIsString($addresses['data'][1]['contacts'][0]['firstName']);
        $this->assertIsString($addresses['data'][1]['contacts'][0]['lastName']);
        $this->assertArrayDoesNotHaveKeys(['name'], $addresses['data'][1]['contacts'][0]);
    }

    #[Test]
    public function it_can_load_custom_fixture_by_filename(): void
    {
        $data = Fixtures::load('drivers.json');

        $this->assertArrayHasKey('data', $data);
        $this->assertIsString($data['data'][0]['id']);
    }

    #[Test]
    public function it_can_load_drivers_fixture(): void
    {
        $drivers = Fixtures::drivers();

        $this->assertArrayHasKey('data', $drivers);
        $this->assertIsArray($drivers['data']);
        $this->assertNotEmpty($drivers['data']);
        $this->assertArrayHasKey('id', $drivers['data'][0]);
        $this->assertIsString($drivers['data'][0]['id']);
        $this->assertIsString($drivers['data'][0]['eldSettings']['rulesets'][0]['cycle']);
        $this->assertIsInt($drivers['data'][0]['carrierSettings']['dotNumber']);
        $this->assertIsString($drivers['data'][0]['tags'][0]['id']);
        $this->assertArrayDoesNotHaveKeys(['tagIds'], $drivers['data'][0]);
    }

    #[Test]
    public function it_can_load_dvirs_fixture(): void
    {
        $dvirs = Fixtures::dvirs();

        $this->assertArrayHasKey('data', $dvirs);
        $this->assertIsArray($dvirs['data']);
        $this->assertNotEmpty($dvirs['data']);
        $this->assertFixtureRowsMatchContractSchema($dvirs, 'DvirStreamResponseDataResponseBody');
        $this->assertIsString($dvirs['data'][0]['type']);
        $this->assertIsString($dvirs['data'][0]['safetyStatus']);
        $this->assertIsString($dvirs['data'][0]['formattedAddress']);
        $this->assertArrayDoesNotHaveKeys(
            [
                'author',
                'inspectionType',
                'safeToOperate',
                'notes',
                'defects',
                'location',
                'startTime',
                'endTime',
                'vehicleDefects',
                'trailerDefects',
            ],
            $dvirs['data'][0]
        );
        $this->assertIsArray($dvirs['data'][1]['defectIds']);
    }

    #[Test]
    public function it_can_load_equipment_fixture(): void
    {
        $equipment = Fixtures::equipment();

        $this->assertArrayHasKey('data', $equipment);
        $this->assertIsArray($equipment['data']);
        $this->assertNotEmpty($equipment['data']);
        $this->assertIsString($equipment['data'][0]['id']);
        $this->assertIsString($equipment['data'][0]['assetSerial']);
        $this->assertIsString($equipment['data'][0]['tags'][0]['id']);
        $this->assertArrayDoesNotHaveKeys(['serial', 'tagIds', 'attributes'], $equipment['data'][0]);
    }

    #[Test]
    public function it_can_load_hos_logs_fixture(): void
    {
        $logs = Fixtures::hosLogs();

        $this->assertArrayHasKey('data', $logs);
        $this->assertIsArray($logs['data']);
        $this->assertNotEmpty($logs['data']);
        $this->assertIsString($logs['data'][0]['driver']['id']);
        $this->assertNotEmpty($logs['data'][0]['hosLogs']);
        $this->assertIsString($logs['data'][0]['hosLogs'][0]['hosStatusType']);
        $this->assertIsString($logs['data'][0]['hosLogs'][0]['vehicle']['id']);
        $this->assertIsArray($logs['data'][0]['hosLogs'][0]['codrivers']);
        $this->assertIsFloat($logs['data'][0]['hosLogs'][0]['logRecordedLocation']['latitude']);
        $this->assertArrayDoesNotHaveKeys(
            ['id', 'driver', 'annotations', 'codriverIds', 'location', 'logRecordedAtTime', 'origin'],
            $logs['data'][0]['hosLogs'][0]
        );
    }

    #[Test]
    public function it_can_load_routes_fixture(): void
    {
        $routes = Fixtures::routes();

        $this->assertArrayHasKey('data', $routes);
        $this->assertIsArray($routes['data']);
        $this->assertNotEmpty($routes['data']);
        $this->assertArrayHasKey('stops', $routes['data'][0]);
        $this->assertIsString($routes['data'][0]['scheduledRouteStartTime']);
        $this->assertIsString($routes['data'][0]['driver']['id']);
        $this->assertIsString($routes['data'][0]['vehicle']['id']);
        $this->assertIsString($routes['data'][0]['stops'][0]['address']['id']);
        $this->assertIsString($routes['data'][0]['stops'][0]['state']);
        $this->assertIsString($routes['data'][0]['stops'][1]['state']);
        $this->assertArrayDoesNotHaveKeys(
            [
                'scheduledStartTime',
                'scheduledEndTime',
                'actualStartTime',
                'actualEndTime',
                'state',
                'assignedDriverId',
                'assignedVehicleId',
            ],
            $routes['data'][0]
        );
        $this->assertArrayDoesNotHaveKeys(['addressId'], $routes['data'][0]['stops'][0]);
        $this->assertArrayDoesNotHaveKeys(['routeStartEndStopOption'], $routes['data'][0]['settings']);
    }

    #[Test]
    public function it_can_load_safety_events_fixture(): void
    {
        $events = Fixtures::safetyEvents();

        $this->assertArrayHasKey('data', $events);
        $this->assertIsArray($events['data']);
        $this->assertNotEmpty($events['data']);
        $this->assertIsString($events['data'][0]['behaviorLabels'][0]['label']);
        $this->assertIsString($events['data'][0]['behaviorLabels'][0]['source']);
        $this->assertIsFloat($events['data'][0]['maxAccelerationGForce']);
        $this->assertIsString($events['data'][0]['coachingState']);
        $this->assertArrayDoesNotHaveKeys(['location'], $events['data'][0]['location']);
        $this->assertArrayDoesNotHaveKeys(
            [
                'behaviorLabel',
                'maxGForce',
                'coachable',
                'downloadedForwardVideoUrl',
                'downloadedInwardVideoUrl',
            ],
            $events['data'][0]
        );
        $this->assertIsString($events['data'][1]['downloadForwardVideoUrl']);
    }

    #[Test]
    public function it_can_load_tags_fixture(): void
    {
        $tags = Fixtures::tags();

        $this->assertArrayHasKey('data', $tags);
        $this->assertIsArray($tags['data']);
        $this->assertNotEmpty($tags['data']);
        $this->assertArrayHasKey('name', $tags['data'][0]);
    }

    #[Test]
    public function it_can_load_trailers_fixture(): void
    {
        $trailers = Fixtures::trailers();

        $this->assertArrayHasKey('data', $trailers);
        $this->assertIsArray($trailers['data']);
        $this->assertNotEmpty($trailers['data']);
        $this->assertIsString($trailers['data'][0]['id']);
        $this->assertIsString($trailers['data'][0]['trailerSerialNumber']);
        $this->assertIsString($trailers['data'][0]['tags'][0]['name']);
        $this->assertArrayDoesNotHaveKeys(['vin', 'tagIds', 'attributes'], $trailers['data'][0]);
    }

    #[Test]
    public function it_can_load_users_fixture(): void
    {
        $users = Fixtures::users();

        $this->assertArrayHasKey('data', $users);
        $this->assertIsArray($users['data']);
        $this->assertNotEmpty($users['data']);
        $this->assertArrayHasKey('email', $users['data'][0]);
        $this->assertIsString($users['data'][0]['authType']);
        $this->assertIsString($users['data'][0]['roles'][0]['role']['id']);
        $this->assertArrayDoesNotHaveKeys(
            ['tagIds', 'createdAtTime', 'lastSignInTime'],
            $users['data'][0]
        );
    }

    #[Test]
    public function it_can_load_vehicle_stats_fixture(): void
    {
        $stats = Fixtures::vehicleStats();

        $this->assertArrayHasKey('data', $stats);
        $this->assertIsArray($stats['data']);
        $this->assertNotEmpty($stats['data']);
        $this->assertArrayHasKey('gps', $stats['data'][0]);
        $this->assertIsString($stats['data'][0]['engineState']['value']);
        $this->assertIsInt($stats['data'][0]['fuelPercent']['value']);
        $this->assertArrayDoesNotHaveKeys(['time', 'engineStates', 'fuelPercents'], $stats['data'][0]);
    }

    #[Test]
    public function it_can_load_vehicles_fixture(): void
    {
        $vehicles = Fixtures::vehicles();

        $this->assertArrayHasKey('data', $vehicles);
        $this->assertIsArray($vehicles['data']);
        $this->assertNotEmpty($vehicles['data']);
        $this->assertFixtureRowsMatchContractSchema($vehicles, 'VehicleResponseObjectResponseBody');
        $this->assertArrayHasKey('id', $vehicles['data'][0]);
        $this->assertIsString($vehicles['data'][0]['createdAtTime']);
        $this->assertIsString($vehicles['data'][0]['id']);
        $this->assertIsString($vehicles['data'][0]['year']);
        $this->assertIsString($vehicles['data'][0]['tags'][0]['name']);
        $this->assertIsArray($vehicles['data'][0]['attributes'][0]['stringValues']);
        $this->assertArrayDoesNotHaveKeys(['tagIds'], $vehicles['data'][0]);
        $this->assertArrayDoesNotHaveKeys(['stringValue'], $vehicles['data'][0]['attributes'][0]);
    }

    #[Test]
    public function it_can_load_webhooks_fixture(): void
    {
        $webhooks = Fixtures::webhooks();

        $this->assertArrayHasKey('data', $webhooks);
        $this->assertIsArray($webhooks['data']);
        $this->assertNotEmpty($webhooks['data']);
        $this->assertArrayHasKey('eventTypes', $webhooks['data'][0]);
        $this->assertIsArray($webhooks['data'][0]['eventTypes']);
        $this->assertIsString($webhooks['data'][0]['version']);
        $this->assertIsString($webhooks['data'][0]['secretKey']);
        $this->assertIsString($webhooks['data'][0]['customHeaders'][0]['key']);
        $this->assertIsString($webhooks['data'][0]['customHeaders'][0]['value']);
        $this->assertArrayDoesNotHaveKeys(
            [
                'isActive',
                'createdAtTime',
                'lastRequestAtTime',
                'lastResponseStatusCode',
                'headers',
                'customMetadata',
            ],
            $webhooks['data'][0]
        );
    }

    #[Test]
    public function it_checks_named_fixtures_against_the_pinned_contract_recursively(): void
    {
        foreach (self::FIXTURE_SCHEMAS as $filename => $schemaName) {
            $fixture = Fixtures::load($filename);
            $this->assertEqualsCanonicalizing(['data', 'pagination'], array_keys($fixture));
            $this->assertIsArray($fixture['data']);
            $this->assertEqualsCanonicalizing(
                ['endCursor', 'hasNextPage'],
                array_keys($fixture['pagination']),
                "Invalid pagination envelope for {$filename}.",
            );
            $this->assertIsString($fixture['pagination']['endCursor']);
            $this->assertIsBool($fixture['pagination']['hasNextPage']);
            $this->assertFixtureRowsMatchContractSchema($fixture, $schemaName);
        }
    }

    #[Test]
    public function it_includes_pagination_data(): void
    {
        $drivers = Fixtures::drivers();

        $this->assertArrayHasKey('pagination', $drivers);
        $this->assertArrayHasKey('endCursor', $drivers['pagination']);
        $this->assertArrayHasKey('hasNextPage', $drivers['pagination']);
    }

    #[Test]
    #[DataProvider('namedFixtures')]
    public function it_loads_each_named_fixture_without_altering_its_json(string $alias, string $filename): void
    {
        $expected = json_decode(
            file_get_contents(dirname(__DIR__, 3).'/src/Testing/Fixtures/'.$filename),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $this->assertSame($expected, Fixtures::{$alias}());
        $this->assertSame($expected, Fixtures::load($filename));
    }

    #[Test]
    public function it_throws_exception_for_missing_fixture(): void
    {
        $this->expectException(RuntimeException::class);

        Fixtures::load('nonexistent.json');
    }

    /** @return array<string, array{string, string}> */
    public static function namedFixtures(): array
    {
        return [
            'addresses'     => ['addresses', 'addresses.json'],
            'drivers'       => ['drivers', 'drivers.json'],
            'dvirs'         => ['dvirs', 'dvirs.json'],
            'equipment'     => ['equipment', 'equipment.json'],
            'hos logs'      => ['hosLogs', 'hos-logs.json'],
            'routes'        => ['routes', 'routes.json'],
            'safety events' => ['safetyEvents', 'safety-events.json'],
            'tags'          => ['tags', 'tags.json'],
            'trailers'      => ['trailers', 'trailers.json'],
            'users'         => ['users', 'users.json'],
            'vehicle stats' => ['vehicleStats', 'vehicle-stats.json'],
            'vehicles'      => ['vehicles', 'vehicles.json'],
            'webhooks'      => ['webhooks', 'webhooks.json'],
        ];
    }

    /**
     * @param  list<string>  $keys
     * @param  array<string, mixed>  $array
     */
    private function assertArrayDoesNotHaveKeys(array $keys, array $array): void
    {
        foreach ($keys as $key) {
            $this->assertArrayNotHasKey($key, $array);
        }
    }

    /**
     * @param  array{data: list<array<string, mixed>>}  $fixture
     */
    private function assertFixtureRowsMatchContractSchema(array $fixture, string $schemaName): void
    {
        $contractPath = dirname(__DIR__, 3).'/contracts/samsara/samsara-api-2026-08-10.json';
        $contract = json_decode(
            (string) file_get_contents($contractPath),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        $schemas = $contract['components']['schemas'];
        $schema = $schemas[$schemaName];

        foreach ($fixture['data'] as $index => $row) {
            $errors = $this->contractSchemaErrors(
                $row,
                $schema,
                $schemas,
                "{$schemaName}[{$index}]",
            );

            $this->assertSame(
                [],
                $errors,
                "Fixture row {$index} does not match {$schemaName}: ".implode('; ', $errors),
            );
        }
    }

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, array<string, mixed>>  $schemas
     * @return list<string>
     */
    private function contractSchemaErrors(
        mixed $value,
        array $schema,
        array $schemas,
        string $path,
    ): array {
        if (isset($schema['$ref'])) {
            $name = basename($schema['$ref']);

            return $this->contractSchemaErrors($value, $schemas[$name], $schemas, $path);
        }

        if (isset($schema['allOf'])) {
            $schema = $this->mergeContractSchemas($schema, $schemas);
        }

        if ($value === null) {
            return ($schema['nullable'] ?? false) ? [] : ["{$path} must not be null"];
        }

        foreach (['oneOf', 'anyOf'] as $alternativesKey) {
            if (! isset($schema[$alternativesKey])) {
                continue;
            }

            foreach ($schema[$alternativesKey] as $alternative) {
                if ($this->contractSchemaErrors($value, $alternative, $schemas, $path) === []) {
                    return [];
                }
            }

            return ["{$path} does not match any {$alternativesKey} schema"];
        }

        if (isset($schema['enum']) && ! in_array($value, $schema['enum'], true)) {
            return ["{$path} has a value outside the contract enum"];
        }

        $type = $schema['type'] ?? (isset($schema['properties']) ? 'object' : null);
        if ($type === 'string' && ! is_string($value)) {
            return ["{$path} must be a string"];
        }
        if ($type === 'integer' && ! is_int($value)) {
            return ["{$path} must be an integer"];
        }
        if ($type === 'number' && ! is_int($value) && ! is_float($value)) {
            return ["{$path} must be a number"];
        }
        if ($type === 'boolean' && ! is_bool($value)) {
            return ["{$path} must be a boolean"];
        }
        if ($type === 'array') {
            if (! is_array($value) || ! array_is_list($value)) {
                return ["{$path} must be an array"];
            }

            $errors = [];
            foreach ($value as $index => $item) {
                $errors = [
                    ...$errors,
                    ...$this->contractSchemaErrors($item, $schema['items'] ?? [], $schemas, "{$path}[{$index}]"),
                ];
            }

            return $errors;
        }
        if ($type !== 'object') {
            return [];
        }
        if (! is_array($value)) {
            return ["{$path} must be an object"];
        }

        $properties = $schema['properties'] ?? [];
        $errors = [];
        foreach ($schema['required'] ?? [] as $required) {
            if (! array_key_exists($required, $value)) {
                $errors[] = "{$path}.{$required} is required";
            }
        }

        foreach ($value as $key => $item) {
            if (isset($properties[$key])) {
                $errors = [
                    ...$errors,
                    ...$this->contractSchemaErrors($item, $properties[$key], $schemas, "{$path}.{$key}"),
                ];

                continue;
            }

            if (is_array($schema['additionalProperties'] ?? null)) {
                $errors = [
                    ...$errors,
                    ...$this->contractSchemaErrors(
                        $item,
                        $schema['additionalProperties'],
                        $schemas,
                        "{$path}.{$key}",
                    ),
                ];
            } elseif ($properties !== [] && ($schema['additionalProperties'] ?? false) !== true) {
                $errors[] = "{$path}.{$key} is not defined by the contract";
            }
        }

        return $errors;
    }

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, array<string, mixed>>  $schemas
     * @return array<string, mixed>
     */
    private function mergeContractSchemas(array $schema, array $schemas): array
    {
        $parts = $schema['allOf'];
        unset($schema['allOf']);

        foreach ($parts as $part) {
            if (isset($part['$ref'])) {
                $part = $schemas[basename($part['$ref'])];
            }
            if (isset($part['allOf'])) {
                $part = $this->mergeContractSchemas($part, $schemas);
            }

            $schema['properties'] = array_merge(
                $schema['properties'] ?? [],
                $part['properties'] ?? [],
            );
            $schema['required'] = array_values(array_unique([
                ...($schema['required'] ?? []),
                ...($part['required'] ?? []),
            ]));

            foreach ($part as $key => $value) {
                if (! in_array($key, ['properties', 'required'], true) && ! isset($schema[$key])) {
                    $schema[$key] = $value;
                }
            }
        }

        return $schema;
    }
}
