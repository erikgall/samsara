<?php

namespace Samsara\Tests\Unit\Data\Form;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Form\FormSubmission;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the FormSubmission entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class FormSubmissionTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $submission = new FormSubmission([
            'id'              => '12345',
            'formTemplateId'  => 'template-1',
            'status'          => 'submitted',
            'submittedAtTime' => '2024-04-10T07:06:25Z',
        ]);

        $this->assertSame('12345', $submission->id);
        $this->assertSame('template-1', $submission->formTemplateId);
        $this->assertSame('submitted', $submission->status);
        $this->assertSame('2024-04-10T07:06:25Z', $submission->submittedAtTime);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $submission = FormSubmission::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(FormSubmission::class, $submission);
        $this->assertSame('12345', $submission->getId());
    }

    #[Test]
    public function it_can_check_if_draft(): void
    {
        $submission = new FormSubmission([
            'status' => 'draft',
        ]);

        $this->assertTrue($submission->isDraft());
        $this->assertFalse($submission->isSubmitted());
    }

    #[Test]
    public function it_can_check_if_submitted(): void
    {
        $submission = new FormSubmission([
            'status' => 'submitted',
        ]);

        $this->assertTrue($submission->isSubmitted());
        $this->assertFalse($submission->isDraft());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'     => '12345',
            'status' => 'submitted',
        ];

        $submission = new FormSubmission($data);

        $this->assertSame($data, $submission->toArray());
    }

    #[Test]
    public function it_can_have_driver(): void
    {
        $submission = new FormSubmission([
            'driver' => [
                'id'   => 'driver-1',
                'name' => 'John Smith',
            ],
        ]);

        $this->assertSame('driver-1', $submission->driver['id']);
    }

    #[Test]
    public function it_can_have_fields(): void
    {
        $submission = new FormSubmission([
            'fields' => [
                ['name' => 'Notes', 'value' => 'Some notes'],
                ['name' => 'Rating', 'value' => '5'],
            ],
        ]);

        $this->assertCount(2, $submission->fields);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $submission = new FormSubmission([
            'createdAtTime'   => '2024-04-10T07:06:25Z',
            'updatedAtTime'   => '2024-04-11T10:00:00Z',
            'submittedAtTime' => '2024-04-10T08:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $submission->createdAtTime);
        $this->assertSame('2024-04-11T10:00:00Z', $submission->updatedAtTime);
        $this->assertSame('2024-04-10T08:00:00Z', $submission->submittedAtTime);
    }

    #[Test]
    public function it_can_have_vehicle(): void
    {
        $submission = new FormSubmission([
            'vehicle' => [
                'id'   => 'vehicle-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('vehicle-1', $submission->vehicle['id']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $submission = new FormSubmission;

        $this->assertInstanceOf(Entity::class, $submission);
    }
}
