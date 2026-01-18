<?php

namespace ErikGall\Samsara\Data\Contact;

use ErikGall\Samsara\Data\Entity;

/**
 * Contact entity.
 *
 * Represents a Samsara contact.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Contact ID
 * @property-read string|null $firstName First name
 * @property-read string|null $lastName Last name
 * @property-read string|null $email Email address
 * @property-read string|null $phone Phone number
 */
class Contact extends Entity
{
    /**
     * Get the full name of the contact.
     */
    public function getFullName(): string
    {
        if (empty($this->firstName) && empty($this->lastName)) {
            return 'Unknown';
        }

        $parts = array_filter([$this->firstName, $this->lastName]);

        return implode(' ', $parts);
    }
}
