<?php

namespace Samsara\Tests\Unit\Data\Document;

use Samsara\Data\Entity;
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
    public function it_can_be_created_with_attributes(): void
    {
        $document = new Document([
            'id'              => '12345',
            'name'            => 'Fuel Receipt',
            'state'           => 'approved',
            'notes'           => 'Fuel purchase for vehicle 123',
            'documentTypeId'  => 'doc-type-1',
            'documentTypeUid' => 'uid-12345',
        ]);

        $this->assertSame('12345', $document->id);
        $this->assertSame('Fuel Receipt', $document->name);
        $this->assertSame('approved', $document->state);
        $this->assertSame('Fuel purchase for vehicle 123', $document->notes);
        $this->assertSame('doc-type-1', $document->documentTypeId);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $document = Document::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame('12345', $document->getId());
    }

    #[Test]
    public function it_can_check_if_approved(): void
    {
        $document = new Document([
            'state' => 'approved',
        ]);

        $this->assertTrue($document->isApproved());
        $this->assertFalse($document->isRejected());
    }

    #[Test]
    public function it_can_check_if_pending(): void
    {
        $document = new Document([
            'state' => 'pending',
        ]);

        $this->assertTrue($document->isPending());
        $this->assertFalse($document->isApproved());
    }

    #[Test]
    public function it_can_check_if_rejected(): void
    {
        $document = new Document([
            'state' => 'rejected',
        ]);

        $this->assertTrue($document->isRejected());
        $this->assertFalse($document->isApproved());
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
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'    => '12345',
            'name'  => 'Fuel Receipt',
            'state' => 'approved',
        ];

        $document = new Document($data);

        $this->assertSame($data, $document->toArray());
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
            'createdAtTime'  => '2024-04-10T07:06:25Z',
            'updatedAtTime'  => '2024-04-11T10:00:00Z',
            'submittedAtUtc' => '2024-04-10T08:00:00Z',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $document->createdAtTime);
        $this->assertSame('2024-04-11T10:00:00Z', $document->updatedAtTime);
        $this->assertSame('2024-04-10T08:00:00Z', $document->submittedAtUtc);
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
    public function it_extends_entity(): void
    {
        $document = new Document;

        $this->assertInstanceOf(Entity::class, $document);
    }
}
