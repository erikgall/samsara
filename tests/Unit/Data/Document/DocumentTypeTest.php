<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Document;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Document\DocumentType;

/**
 * Unit tests for the DocumentType entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DocumentTypeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $documentType = new DocumentType([
            'id'                     => '12345',
            'uid'                    => 'uid-12345',
            'name'                   => 'Fuel Receipt',
            'orgId'                  => 'org-1',
            'fieldDefinitions'       => [],
            'conditionSets'          => [],
            'isDriverEditable'       => true,
            'requireDriverSignature' => false,
        ]);

        $this->assertSame('12345', $documentType->id);
        $this->assertSame('uid-12345', $documentType->uid);
        $this->assertSame('Fuel Receipt', $documentType->name);
        $this->assertTrue($documentType->isDriverEditable);
        $this->assertFalse($documentType->requireDriverSignature);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $documentType = DocumentType::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(DocumentType::class, $documentType);
        $this->assertSame('12345', $documentType->getId());
    }

    #[Test]
    public function it_can_check_if_driver_editable(): void
    {
        $documentType = new DocumentType([
            'isDriverEditable' => true,
        ]);

        $this->assertTrue($documentType->isDriverEditable());
    }

    #[Test]
    public function it_can_check_if_requires_signature(): void
    {
        $documentType = new DocumentType([
            'requireDriverSignature' => true,
        ]);

        $this->assertTrue($documentType->requiresSignature());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Fuel Receipt',
        ];

        $documentType = new DocumentType($data);

        $this->assertSame($data, $documentType->toArray());
    }

    #[Test]
    public function it_can_have_field_definitions(): void
    {
        $documentType = new DocumentType([
            'fieldDefinitions' => [
                ['name' => 'Fuel Type', 'type' => 'string'],
                ['name' => 'Gallons', 'type' => 'number'],
            ],
        ]);

        $this->assertCount(2, $documentType->fieldDefinitions);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $documentType = new DocumentType;

        $this->assertInstanceOf(Entity::class, $documentType);
    }

    #[Test]
    public function it_returns_false_when_driver_editable_not_set(): void
    {
        $documentType = new DocumentType;

        $this->assertFalse($documentType->isDriverEditable());
    }

    #[Test]
    public function it_returns_false_when_requires_signature_not_set(): void
    {
        $documentType = new DocumentType;

        $this->assertFalse($documentType->requiresSignature());
    }
}
