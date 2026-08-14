<?php

namespace Samsara\Tests\Unit\Http;

use Samsara\Http\QueryString;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class QueryStringTest extends TestCase
{
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
