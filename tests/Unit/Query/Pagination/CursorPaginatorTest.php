<?php

namespace Samsara\Tests\Unit\Query\Pagination;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;
use Samsara\Query\Pagination\Cursor;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Query\Pagination\CursorPaginator;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the CursorPaginator class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class CursorPaginatorTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated(): void
    {
        $items = new EntityCollection([
            Entity::make(['id' => '1']),
        ]);
        $cursor = new Cursor('abc-123', true);
        $builder = $this->createBuilder();

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $this->assertInstanceOf(CursorPaginator::class, $paginator);
    }

    #[Test]
    public function it_can_get_next_page(): void
    {
        $fakeHttp = new HttpFactory;
        $fakeHttp->fake([
            '*' => $fakeHttp->response([
                'data' => [
                    ['id' => '3', 'name' => 'Third'],
                    ['id' => '4', 'name' => 'Fourth'],
                ],
                'pagination' => [
                    'endCursor'   => 'cursor-page-2',
                    'hasNextPage' => false,
                ],
            ], 200),
        ]);

        $samsara = new Samsara('test-token');
        $samsara->setHttpFactory($fakeHttp);
        $resource = new TestPaginatorResource($samsara);
        $builder = new Builder($resource);

        $items = new EntityCollection([
            Entity::make(['id' => '1']),
            Entity::make(['id' => '2']),
        ]);
        $cursor = new Cursor('cursor-page-1', true);

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $nextPage = $paginator->nextPage();

        $this->assertInstanceOf(CursorPaginator::class, $nextPage);
        $this->assertCount(2, $nextPage->items());
        $this->assertSame('3', $nextPage->items()->first()->getId());
        $this->assertFalse($nextPage->hasMorePages());
    }

    #[Test]
    public function it_checks_has_more_pages(): void
    {
        $items = new EntityCollection([]);
        $cursor = new Cursor('abc-123', true);
        $builder = $this->createBuilder();

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $this->assertTrue($paginator->hasMorePages());
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $items = new EntityCollection([
            Entity::make(['id' => '1', 'name' => 'First']),
        ]);
        $cursor = new Cursor('abc-123', true);
        $builder = $this->createBuilder();

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $array = $paginator->toArray();

        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('pagination', $array);
        $this->assertCount(1, $array['data']);
        $this->assertSame('abc-123', $array['pagination']['endCursor']);
    }

    #[Test]
    public function it_is_countable(): void
    {
        $items = new EntityCollection([
            Entity::make(['id' => '1']),
            Entity::make(['id' => '2']),
            Entity::make(['id' => '3']),
        ]);
        $cursor = new Cursor('abc-123', true);
        $builder = $this->createBuilder();

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $this->assertCount(3, $paginator);
    }

    #[Test]
    public function it_is_iterable(): void
    {
        $items = new EntityCollection([
            Entity::make(['id' => '1']),
            Entity::make(['id' => '2']),
        ]);
        $cursor = new Cursor('abc-123', true);
        $builder = $this->createBuilder();

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $ids = [];
        foreach ($paginator as $item) {
            $ids[] = $item->getId();
        }

        $this->assertSame(['1', '2'], $ids);
    }

    #[Test]
    public function it_returns_cursor(): void
    {
        $items = new EntityCollection([]);
        $cursor = new Cursor('abc-123', true);
        $builder = $this->createBuilder();

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $this->assertSame($cursor, $paginator->cursor());
    }

    #[Test]
    public function it_returns_false_when_no_more_pages(): void
    {
        $items = new EntityCollection([]);
        $cursor = new Cursor('abc-123', false);
        $builder = $this->createBuilder();

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $this->assertFalse($paginator->hasMorePages());
    }

    #[Test]
    public function it_returns_items(): void
    {
        $items = new EntityCollection([
            Entity::make(['id' => '1']),
            Entity::make(['id' => '2']),
        ]);
        $cursor = new Cursor('abc-123', true);
        $builder = $this->createBuilder();

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $this->assertSame($items, $paginator->items());
        $this->assertCount(2, $paginator->items());
    }

    #[Test]
    public function it_returns_null_when_no_more_pages_on_next_page(): void
    {
        $items = new EntityCollection([]);
        $cursor = new Cursor('abc-123', false);
        $builder = $this->createBuilder();

        $paginator = new CursorPaginator($items, $cursor, $builder);

        $this->assertNull($paginator->nextPage());
    }

    /**
     * Create a builder instance for testing.
     */
    protected function createBuilder(): Builder
    {
        $samsara = new Samsara('test-token');
        $resource = new TestPaginatorResource($samsara);

        return new Builder($resource);
    }
}

/**
 * Test resource for CursorPaginator tests.
 */
class TestPaginatorResource extends Resource
{
    protected string $endpoint = '/test-paginator-endpoint';

    protected string $entity = Entity::class;

    public function client(): PendingRequest
    {
        return $this->samsara->client();
    }
}
