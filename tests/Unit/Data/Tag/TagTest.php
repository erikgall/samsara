<?php

namespace Samsara\Tests\Unit\Data\Tag;

use Samsara\Data\Tag\Tag;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Tag entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TagTest extends TestCase
{
    #[Test]
    public function it_can_check_if_has_parent(): void
    {
        $tagWithParentId = new Tag(['parentTagId' => '4815']);
        $tagWithNestedParent = new Tag(['parentTag' => ['id' => '4815', 'name' => 'West Coast']]);

        $this->assertTrue($tagWithParentId->hasParent());
        $this->assertTrue($tagWithNestedParent->hasParent());
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
    public function it_gets_an_exact_string_external_id(): void
    {
        $tag = new Tag([
            'externalIds' => [
                'regionId' => 'R1',
                'empty'    => '',
            ],
        ]);

        $this->assertSame('R1', $tag->getExternalId('regionId'));
        $this->assertSame('', $tag->getExternalId('empty'));
    }

    #[Test]
    public function it_gets_the_parent_as_a_tag(): void
    {
        $tag = new Tag([
            'parentTag' => [
                'id'   => '4815',
                'name' => 'West Coast',
            ],
        ]);

        $parent = $tag->getParent();

        $this->assertInstanceOf(Tag::class, $parent);
        $this->assertSame('4815', $parent->getId());
        $this->assertSame('West Coast', $parent->name);
    }

    #[Test]
    public function it_returns_false_when_no_parent(): void
    {
        $tags = [
            new Tag,
            new Tag(['parentTagId' => null, 'parentTag' => null]),
            new Tag(['parentTagId' => '', 'parentTag' => []]),
            new Tag(['parentTagId' => 4815, 'parentTag' => 'malformed']),
        ];

        foreach ($tags as $tag) {
            $this->assertFalse($tag->hasParent());
        }
    }

    #[Test]
    public function it_returns_null_for_missing_or_malformed_external_ids(): void
    {
        $tags = [
            new Tag,
            new Tag(['externalIds' => null]),
            new Tag(['externalIds' => 'malformed']),
            new Tag(['externalIds' => []]),
            new Tag(['externalIds' => ['regionId' => 123]]),
        ];

        foreach ($tags as $tag) {
            $this->assertNull($tag->getExternalId('regionId'));
        }
    }

    #[Test]
    public function it_returns_null_for_missing_or_malformed_parent_tags(): void
    {
        $tags = [
            new Tag,
            new Tag(['parentTag' => null]),
            new Tag(['parentTag' => 'malformed']),
            new Tag(['parentTag' => []]),
        ];

        foreach ($tags as $tag) {
            $this->assertNull($tag->getParent());
        }
    }
}
