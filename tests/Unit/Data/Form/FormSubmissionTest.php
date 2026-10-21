<?php

namespace Samsara\Tests\Unit\Data\Form;

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
    public function it_can_check_each_documented_status(): void
    {
        $statusMethods = [
            'notStarted'       => 'isNotStarted',
            'completed'        => 'isCompleted',
            'archived'         => 'isArchived',
            'inProgress'       => 'isInProgress',
            'needsReview'      => 'needsReview',
            'changesRequested' => 'hasChangesRequested',
            'approved'         => 'isApproved',
        ];

        foreach ($statusMethods as $status => $expectedMethod) {
            $submission = new FormSubmission(['status' => $status]);

            foreach ($statusMethods as $method) {
                $this->assertSame($method === $expectedMethod, $submission->{$method}());
            }
        }
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
    public function it_can_have_an_asset(): void
    {
        $submission = new FormSubmission([
            'asset' => [
                'id'   => 'asset-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('asset-1', $submission->asset['id']);
    }

    #[Test]
    public function it_can_have_an_assignee(): void
    {
        $submission = new FormSubmission([
            'assignedTo' => [
                'id'   => 'driver-1',
                'name' => 'John Smith',
                'type' => 'driver',
            ],
        ]);

        $this->assertSame('driver-1', $submission->assignedTo['id']);
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
    public function it_documented_status_checks_require_an_exact_known_value(): void
    {
        $methods = [
            'isNotStarted',
            'isCompleted',
            'isArchived',
            'isInProgress',
            'needsReview',
            'hasChangesRequested',
            'isApproved',
        ];
        $attributes = [
            [],
            ['status' => null],
            ['status' => ''],
            ['status' => 'unknown'],
            ['status' => 'submitted'],
        ];

        foreach ($attributes as $values) {
            $submission = new FormSubmission($values);

            foreach ($methods as $method) {
                $this->assertFalse($submission->{$method}());
            }
        }
    }
}
