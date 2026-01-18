<?php

namespace ErikGall\Samsara\Tests\Unit\Query\Pagination;

use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Query\Pagination\Cursor;

/**
 * Unit tests for the Cursor class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class CursorTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_response_data(): void
    {
        $data = [
            'endCursor'   => 'cursor-xyz-789',
            'hasNextPage' => true,
        ];

        $cursor = Cursor::fromResponse($data);

        $this->assertSame('cursor-xyz-789', $cursor->getEndCursor());
        $this->assertTrue($cursor->hasNextPage());
    }

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $cursor = new Cursor('abc-123', true);

        $this->assertInstanceOf(Cursor::class, $cursor);
    }

    #[Test]
    public function it_can_have_null_end_cursor(): void
    {
        $cursor = new Cursor(null, false);

        $this->assertNull($cursor->getEndCursor());
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $cursor = new Cursor('abc-123', true);

        $array = $cursor->toArray();

        $this->assertSame([
            'endCursor'   => 'abc-123',
            'hasNextPage' => true,
        ], $array);
    }

    #[Test]
    public function it_handles_missing_pagination_data(): void
    {
        $data = [];

        $cursor = Cursor::fromResponse($data);

        $this->assertNull($cursor->getEndCursor());
        $this->assertFalse($cursor->hasNextPage());
    }

    #[Test]
    public function it_returns_end_cursor(): void
    {
        $cursor = new Cursor('abc-123', true);

        $this->assertSame('abc-123', $cursor->getEndCursor());
    }

    #[Test]
    public function it_returns_false_for_has_next_page_when_false(): void
    {
        $cursor = new Cursor('abc-123', false);

        $this->assertFalse($cursor->hasNextPage());
    }

    #[Test]
    public function it_returns_has_next_page(): void
    {
        $cursor = new Cursor('abc-123', true);

        $this->assertTrue($cursor->hasNextPage());
    }
}
