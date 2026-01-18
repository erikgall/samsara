<?php

namespace Samsara\Tests\Unit\Data\Form;

use Samsara\Data\Entity;
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
    public function it_can_be_created_with_attributes(): void
    {
        $template = new FormTemplate([
            'id'          => '12345',
            'name'        => 'Pre-Trip Inspection',
            'description' => 'Standard pre-trip inspection form',
            'revision'    => 1,
        ]);

        $this->assertSame('12345', $template->id);
        $this->assertSame('Pre-Trip Inspection', $template->name);
        $this->assertSame('Standard pre-trip inspection form', $template->description);
        $this->assertSame(1, $template->revision);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $template = FormTemplate::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(FormTemplate::class, $template);
        $this->assertSame('12345', $template->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Pre-Trip Inspection',
        ];

        $template = new FormTemplate($data);

        $this->assertSame($data, $template->toArray());
    }

    #[Test]
    public function it_can_have_field_definitions(): void
    {
        $template = new FormTemplate([
            'fieldDefinitions' => [
                ['name' => 'Notes', 'type' => 'text'],
                ['name' => 'Rating', 'type' => 'number'],
            ],
        ]);

        $this->assertCount(2, $template->fieldDefinitions);
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

    #[Test]
    public function it_extends_entity(): void
    {
        $template = new FormTemplate;

        $this->assertInstanceOf(Entity::class, $template);
    }
}
