<?php

namespace Samsara\Tests\Unit\Http;

use Samsara\Http\QueryString;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <erik@erikgall.com>
 */
class QueryStringTest extends TestCase
{
    #[Test]
    public function it_encodes_repeated_values_without_losing_reserved_characters_or_existing_queries(): void
    {
        [$endpoint, $query] = QueryString::prepare(
            '/alerts/configurations?after=existing',
            ['ids' => ['a/b', 'c d+e&f'], 'enabled' => true, 'archived' => false],
            ['ids'],
        );

        $this->assertSame('/alerts/configurations?after=existing&ids=a%2Fb&ids=c%20d%2Be%26f&enabled=true&archived=false', $endpoint);
        $this->assertSame([], $query);
    }

    #[Test]
    public function it_keeps_csv_filters_distinct_from_repeated_values(): void
    {
        [$endpoint, $query] = QueryString::prepare(
            '/events',
            ['vehicleIds' => 'vehicle-1,vehicle-2', 'states' => ['Requested', 'Approved']],
            ['states'],
        );

        $this->assertSame('/events?states=Requested&states=Approved&vehicleIds=vehicle-1%2Cvehicle-2', $endpoint);
        $this->assertSame([], $query);
    }

    #[Test]
    public function it_omits_empty_repeated_lists_and_preserves_scalar_parameters(): void
    {
        $this->assertSame(['/events', ['state' => 'Requested']], QueryString::prepare(
            '/events',
            ['ids' => [], 'state' => 'Requested'],
            ['ids', 'state', 'missing'],
        ));
    }

    #[Test]
    public function it_prepares_repeated_and_standard_query_values_for_one_request_url(): void
    {
        [$endpoint, $query] = QueryString::prepare(
            '/events',
            [
                'states' => ['Requested', 'Approved'],
                'search' => 'north hub',
            ],
            ['states'],
        );

        $this->assertSame(
            '/events?states=Requested&states=Approved&search=north%20hub',
            $endpoint,
        );
        $this->assertSame([], $query);
    }

    #[Test]
    public function it_preserves_arbitrary_arrays_and_serializes_booleans_for_openapi_queries(): void
    {
        [$endpoint, $query] = QueryString::prepare(
            '/events',
            [
                'ids'       => ['asset-1', 'asset-2'],
                'includeVg' => true,
                'archived'  => false,
            ],
        );

        $this->assertSame('/events', $endpoint);
        $this->assertSame([
            'ids'       => ['asset-1', 'asset-2'],
            'includeVg' => 'true',
            'archived'  => 'false',
        ], $query);
    }
}
