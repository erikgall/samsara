<?php

namespace Samsara\Tests\Unit\Data\Form;

use Samsara\Tests\TestCase;
use Samsara\Data\Form\FormTemplate;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the FormTemplate entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class FormTemplateTest extends TestCase
{
    #[Test]
    public function it_can_count_fields_and_sections(): void
    {
        $template = new FormTemplate([
            'fields' => [
                ['id' => 'field-1'],
                ['id' => 'field-2'],
            ],
            'sections' => [
                ['id' => 'section-1'],
            ],
        ]);

        $this->assertSame(2, $template->getFieldCount());
        $this->assertSame(1, $template->getSectionCount());
        $this->assertSame(0, (new FormTemplate)->getFieldCount());
        $this->assertSame(0, (new FormTemplate(['fields' => null]))->getFieldCount());
        $this->assertSame(0, (new FormTemplate(['sections' => 'invalid']))->getSectionCount());
    }

    #[Test]
    public function it_can_have_field_definitions(): void
    {
        $template = new FormTemplate([
            'fields' => [
                ['name' => 'Notes', 'type' => 'text'],
                ['name' => 'Rating', 'type' => 'number'],
            ],
        ]);

        $this->assertCount(2, $template->fields);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $template = new FormTemplate([
            'createdAtTime' => '2024-04-10T07:06:25Z',
            'updatedAtTime' => '2024-04-11T10:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $template->createdAtTime);
        $this->assertSame('2024-04-11T10:00:00Z', $template->updatedAtTime);
    }
}
