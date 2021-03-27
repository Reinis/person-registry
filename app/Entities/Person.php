<?php

namespace PersonRegistry\Entities;

use InvalidArgumentException;

class Person
{
    private string $firstName;
    private string $lastName;
    private string $nationalId;
    private string $notes;

    public function __construct(string $firstName, string $lastName, string $nationalId, string $notes = '')
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->setNationalId($nationalId);
        $this->notes = $notes;
    }

    private function setNationalId(string $nationalId): void
    {
        $match = preg_match('/^\d{6}[-]?\d{5}$/', $nationalId);

        if (0 === $match || false === $match) {
            throw new InvalidArgumentException("Invalid National Identification Number");
        }

        $this->nationalId = $nationalId;
    }

    public function getName(): string
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getNationalId(): string
    {
        return $this->nationalId;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }
}
