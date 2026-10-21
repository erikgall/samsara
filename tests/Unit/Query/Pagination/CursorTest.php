<?php

namespace Samsara\Tests\Unit\Query\Pagination;

use Error;
use Samsara\Tests\TestCase;
use Samsara\Query\Pagination\Cursor;
use PHPUnit\Framework\Attributes\Test;

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

        $this->assertSame('cursor-xyz-789', $cursor->endCursor);
        $this->assertTrue($cursor->hasNextPage);
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

        $this->assertNull($cursor->endCursor);
    }

    #[Test]
    public function it_cannot_change_whether_another_page_exists(): void
    {
        $cursor = new Cursor(null, false);
        $this->expectException(Error::class);

        $cursor->hasNextPage = true;
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
    public function it_exposes_cursor_state_as_readonly_properties(): void
    {
        $cursor = new Cursor('abc-123', true);

        $this->assertSame('abc-123', $cursor->endCursor);
        $this->assertTrue($cursor->hasNextPage);
        $this->expectException(Error::class);

        $cursor->endCursor = 'changed';
    }

    #[Test]
    public function it_handles_missing_pagination_data(): void
    {
        $data = [];

        $cursor = Cursor::fromResponse($data);

        $this->assertNull($cursor->endCursor);
        $this->assertFalse($cursor->hasNextPage);
    }

    #[Test]
    public function it_returns_end_cursor(): void
    {
        $cursor = new Cursor('abc-123', true);

        $this->assertSame('abc-123', $cursor->endCursor);
    }

    #[Test]
    public function it_returns_false_for_has_next_page_when_false(): void
    {
        $cursor = new Cursor('abc-123', false);

        $this->assertFalse($cursor->hasNextPage);
    }

    #[Test]
    public function it_returns_has_next_page(): void
    {
        $cursor = new Cursor('abc-123', true);

        $this->assertTrue($cursor->hasNextPage);
    }
}
