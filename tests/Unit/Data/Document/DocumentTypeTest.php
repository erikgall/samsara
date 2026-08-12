<?php

namespace Samsara\Tests\Unit\Data\Document;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Document\DocumentType;

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
            'id'                       => '12345',
            'name'                     => 'Fuel Receipt',
            'orgId'                    => 12345,
            'fieldTypes'               => [],
            'conditionalFieldSections' => [],
        ]);

        $this->assertSame('12345', $documentType->id);
        $this->assertSame('Fuel Receipt', $documentType->name);
        $this->assertSame(12345, $documentType->orgId);
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
    public function it_can_have_field_types(): void
    {
        $documentType = new DocumentType([
            'fieldTypes' => [
                ['fieldType' => 'multipleChoice', 'label' => 'Fuel Type', 'requiredField' => true],
                ['fieldType' => 'number', 'label' => 'Gallons', 'requiredField' => false],
            ],
        ]);

        $this->assertCount(2, $documentType->fieldTypes);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $documentType = new DocumentType;

        $this->assertInstanceOf(Entity::class, $documentType);
    }

    #[Test]
    public function it_gets_only_required_field_types(): void
    {
        $required = ['fieldType' => 'signature', 'label' => 'Sign', 'requiredField' => true];
        $documentType = new DocumentType([
            'fieldTypes' => [
                ['fieldType' => 'number', 'label' => 'Gallons', 'requiredField' => false],
                $required,
                ['fieldType' => 'text', 'label' => 'Notes'],
            ],
        ]);

        $this->assertSame([$required], $documentType->getRequiredFieldTypes());
        $this->assertSame([], (new DocumentType)->getRequiredFieldTypes());
        $this->assertSame([], (new DocumentType(['fieldTypes' => null]))->getRequiredFieldTypes());
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

    #[Test]
    public function legacy_driver_editable_check_remains_backwards_compatible(): void
    {
        $documentType = new DocumentType([
            'isDriverEditable' => true,
        ]);

        $this->assertTrue($documentType->isDriverEditable());
    }

    #[Test]
    public function legacy_signature_check_remains_backwards_compatible(): void
    {
        $documentType = new DocumentType([
            'requireDriverSignature' => true,
        ]);

        $this->assertTrue($documentType->requiresSignature());
    }
}
