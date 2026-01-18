<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Contact;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Contact\Contact;

/**
 * Unit tests for the Contact entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ContactTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $contact = new Contact([
            'id'        => '22408',
            'firstName' => 'Jane',
            'lastName'  => 'Jones',
            'email'     => 'jane.jones@example.com',
            'phone'     => '5558234327',
        ]);

        $this->assertSame('22408', $contact->id);
        $this->assertSame('Jane', $contact->firstName);
        $this->assertSame('Jones', $contact->lastName);
        $this->assertSame('jane.jones@example.com', $contact->email);
        $this->assertSame('5558234327', $contact->phone);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $contact = Contact::make([
            'id'        => '22408',
            'firstName' => 'Jane',
        ]);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame('22408', $contact->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'        => '22408',
            'firstName' => 'Jane',
            'lastName'  => 'Jones',
        ];

        $contact = new Contact($data);

        $this->assertSame($data, $contact->toArray());
    }

    #[Test]
    public function it_can_get_full_name(): void
    {
        $contact = new Contact([
            'firstName' => 'Jane',
            'lastName'  => 'Jones',
        ]);

        $this->assertSame('Jane Jones', $contact->getFullName());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $contact = new Contact;

        $this->assertInstanceOf(Entity::class, $contact);
    }

    #[Test]
    public function it_returns_first_name_only_when_no_last_name(): void
    {
        $contact = new Contact([
            'firstName' => 'Jane',
        ]);

        $this->assertSame('Jane', $contact->getFullName());
    }

    #[Test]
    public function it_returns_unknown_when_no_name(): void
    {
        $contact = new Contact;

        $this->assertSame('Unknown', $contact->getFullName());
    }
}
