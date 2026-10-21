<?php

namespace Samsara\Tests\Unit\Data\Document;

use Samsara\Tests\TestCase;
use Samsara\Data\Document\Document;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Document entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DocumentTest extends TestCase
{
    #[Test]
    public function it_can_check_each_current_document_state(): void
    {
        $states = [
            'submitted' => 'isSubmitted',
            'required'  => 'isRequired',
            'archived'  => 'isArchived',
        ];

        foreach ($states as $state => $expectedMethod) {
            $document = new Document(['state' => $state]);

            foreach ($states as $method) {
                $this->assertSame($method === $expectedMethod, $document->{$method}());
            }
        }

        foreach ([[], ['state' => null], ['state' => ''], ['state' => 'unknown']] as $attributes) {
            $document = new Document($attributes);

            foreach ($states as $method) {
                $this->assertFalse($document->{$method}());
            }
        }
    }

    #[Test]
    public function it_can_check_if_required(): void
    {
        $document = new Document([
            'state' => 'required',
        ]);

        $this->assertTrue($document->isRequired());
    }

    #[Test]
    public function it_can_have_driver(): void
    {
        $document = new Document([
            'driver' => [
                'id'   => 'driver-1',
                'name' => 'John Smith',
            ],
        ]);

        $this->assertSame('driver-1', $document->driver['id']);
        $this->assertSame('John Smith', $document->driver['name']);
    }

    #[Test]
    public function it_can_have_fields(): void
    {
        $document = new Document([
            'fields' => [
                ['name' => 'Fuel Type', 'value' => 'Diesel'],
                ['name' => 'Gallons', 'value' => '50'],
            ],
        ]);

        $this->assertCount(2, $document->fields);
        $this->assertSame('Fuel Type', $document->fields[0]['name']);
    }

    #[Test]
    public function it_can_have_timestamps(): void
    {
        $document = new Document([
            'createdAtTime' => '2024-04-10T07:06:25Z',
            'updatedAtTime' => '2024-04-11T10:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $document->createdAtTime);
        $this->assertSame('2024-04-11T10:00:00Z', $document->updatedAtTime);
    }

    #[Test]
    public function it_can_have_vehicle(): void
    {
        $document = new Document([
            'vehicle' => [
                'id'   => 'vehicle-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('vehicle-1', $document->vehicle['id']);
    }

    #[Test]
    public function it_legacy_approved_state_check_remains_backwards_compatible(): void
    {
        $document = new Document([
            'state' => 'approved',
        ]);

        $this->assertTrue($document->isApproved());
        $this->assertFalse($document->isRejected());
    }

    #[Test]
    public function it_legacy_pending_state_check_remains_backwards_compatible(): void
    {
        $document = new Document([
            'state' => 'pending',
        ]);

        $this->assertTrue($document->isPending());
        $this->assertFalse($document->isApproved());
    }

    #[Test]
    public function it_legacy_rejected_state_check_remains_backwards_compatible(): void
    {
        $document = new Document([
            'state' => 'rejected',
        ]);

        $this->assertTrue($document->isRejected());
        $this->assertFalse($document->isApproved());
    }
}
