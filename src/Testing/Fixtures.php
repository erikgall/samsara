<?php

namespace ErikGall\Samsara\Testing;

use RuntimeException;

/**
 * Test fixtures loader.
 *
 * Provides convenient access to JSON fixture files for testing.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class Fixtures
{
    /**
     * The path to the fixtures directory.
     */
    protected static ?string $fixturesPath = null;

    /**
     * Get the addresses fixture.
     *
     * @return array<string, mixed>
     */
    public static function addresses(): array
    {
        return static::load('addresses.json');
    }

    /**
     * Get the drivers fixture.
     *
     * @return array<string, mixed>
     */
    public static function drivers(): array
    {
        return static::load('drivers.json');
    }

    /**
     * Get the DVIRs fixture.
     *
     * @return array<string, mixed>
     */
    public static function dvirs(): array
    {
        return static::load('dvirs.json');
    }

    /**
     * Get the equipment fixture.
     *
     * @return array<string, mixed>
     */
    public static function equipment(): array
    {
        return static::load('equipment.json');
    }

    /**
     * Get the path to the fixtures directory.
     */
    public static function getFixturesPath(): string
    {
        if (static::$fixturesPath === null) {
            static::$fixturesPath = __DIR__.'/Fixtures';
        }

        return static::$fixturesPath;
    }

    /**
     * Get the HOS logs fixture.
     *
     * @return array<string, mixed>
     */
    public static function hosLogs(): array
    {
        return static::load('hos-logs.json');
    }

    /**
     * Load a fixture file.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException If the fixture file doesn't exist or is invalid
     */
    public static function load(string $filename): array
    {
        $path = static::getFixturesPath().'/'.$filename;

        if (! file_exists($path)) {
            throw new RuntimeException("Fixture file not found: {$filename}");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Failed to read fixture file: {$filename}");
        }

        $data = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                "Invalid JSON in fixture file {$filename}: ".json_last_error_msg()
            );
        }

        return $data;
    }

    /**
     * Get the routes fixture.
     *
     * @return array<string, mixed>
     */
    public static function routes(): array
    {
        return static::load('routes.json');
    }

    /**
     * Get the safety events fixture.
     *
     * @return array<string, mixed>
     */
    public static function safetyEvents(): array
    {
        return static::load('safety-events.json');
    }

    /**
     * Set a custom fixtures path.
     */
    public static function setFixturesPath(string $path): void
    {
        static::$fixturesPath = $path;
    }

    /**
     * Get the tags fixture.
     *
     * @return array<string, mixed>
     */
    public static function tags(): array
    {
        return static::load('tags.json');
    }

    /**
     * Get the trailers fixture.
     *
     * @return array<string, mixed>
     */
    public static function trailers(): array
    {
        return static::load('trailers.json');
    }

    /**
     * Get the users fixture.
     *
     * @return array<string, mixed>
     */
    public static function users(): array
    {
        return static::load('users.json');
    }

    /**
     * Get the vehicles fixture.
     *
     * @return array<string, mixed>
     */
    public static function vehicles(): array
    {
        return static::load('vehicles.json');
    }

    /**
     * Get the vehicle stats fixture.
     *
     * @return array<string, mixed>
     */
    public static function vehicleStats(): array
    {
        return static::load('vehicle-stats.json');
    }

    /**
     * Get the webhooks fixture.
     *
     * @return array<string, mixed>
     */
    public static function webhooks(): array
    {
        return static::load('webhooks.json');
    }
}
