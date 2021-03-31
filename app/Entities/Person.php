<?php

namespace PersonRegistry\Entities;

use InvalidArgumentException;

class Person
{
    private int $id = 0;
    private string $firstName;
    private string $lastName;
    private string $nationalId;
    private int $age;
    private string $address;
    private string $notes;

    public function __construct(string $firstName, string $lastName, string $nationalId, int $age = 0, string $address = '', string $notes = '')
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->setNationalId($nationalId);
        $this->age = $age;
        $this->address = $address;
        $this->notes = $notes;
    }

    private function setNationalId(string $nationalId): void
    {
        self::validateNationalId($nationalId);

        // Normalize
        $nationalId = (string)preg_replace('/^(\d{6})[-]?(\d{5})$/', '$1-$2', $nationalId);

        $this->nationalId = $nationalId;
    }

    private static function validateNationalId(string $nationalId): void
    {
        $match = preg_match('/^\d{6}[-]?\d{5}$/', $nationalId);

        if (0 === $match || false === $match) {
            throw new InvalidArgumentException("Invalid National Identification Number: {$nationalId}");
        }
    }

    public static function isValidNationalId(string $nationalId): bool
    {
        try {
            self::validateNationalId($nationalId);
        } catch (InvalidArgumentException $exception) {
            return false;
        }

        return true;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getNationalId(): string
    {
        return $this->nationalId;
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

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
