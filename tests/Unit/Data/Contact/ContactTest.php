<?php

namespace Samsara\Tests\Unit\Data\Contact;

use Samsara\Tests\TestCase;
use Samsara\Data\Contact\Contact;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Contact entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ContactTest extends TestCase
{
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
