<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Tag;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Data\Tag\Tag;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Tag entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TagTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $tag = new Tag([
            'id'          => '3914',
            'name'        => 'East Coast',
            'parentTagId' => '4815',
        ]);

        $this->assertSame('3914', $tag->id);
        $this->assertSame('East Coast', $tag->name);
        $this->assertSame('4815', $tag->parentTagId);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $tag = Tag::make([
            'id'   => '3914',
            'name' => 'East Coast',
        ]);

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertSame('3914', $tag->getId());
    }

    #[Test]
    public function it_can_check_if_has_parent(): void
    {
        $tag = new Tag([
            'parentTagId' => '4815',
        ]);

        $this->assertTrue($tag->hasParent());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '3914',
            'name' => 'East Coast',
        ];

        $tag = new Tag($data);

        $this->assertSame($data, $tag->toArray());
    }

    #[Test]
    public function it_can_have_external_ids(): void
    {
        $tag = new Tag([
            'externalIds' => [
                'regionId' => 'R1',
            ],
        ]);

        $this->assertSame('R1', $tag->externalIds['regionId']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $tag = new Tag;

        $this->assertInstanceOf(Entity::class, $tag);
    }

    #[Test]
    public function it_returns_false_when_no_parent(): void
    {
        $tag = new Tag;

        $this->assertFalse($tag->hasParent());
    }
}
