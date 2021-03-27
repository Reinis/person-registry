<?php

namespace PersonRegistryTest;

use InvalidArgumentException;
use PersonRegistry\Entities\Person;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testName(): void
    {
        $name = "John";
        $surname = "Doe";
        $nid = "123456-12345";
        $person = new Person($name, $surname, $nid);

        self::assertEquals($name, $person->getFirstName());
        self::assertEquals($surname, $person->getLastName());
        self::assertEquals("{$name} {$surname}", $person->getName());
    }

    public function testNID(): void
    {
        $name = "John";
        $surname = "Doe";
        $nid1 = "123456-12345";
        $nid2 = "12345612345";
        $person1 = new Person($name, $surname, $nid1);
        $person2 = new Person($name, $surname, $nid2);

        self::assertEquals($nid1, $person1->getNationalId());
        self::assertEquals($nid2, $person2->getNationalId());
    }

    public function testInvalidNID(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid National Identification Number");

        $name = "John";
        $surname = "Doe";
        $nid = "123456 12345";

        new Person($name, $surname, $nid);
    }

    public function testNotesEmpty(): Person
    {
        $name = "John";
        $surname = "Doe";
        $nid = "123456-12345";
        $person = new Person($name, $surname, $nid);

        self::assertEmpty($person->getNotes());

        return $person;
    }

    /**
     * @depends testNotesEmpty
     * @param Person $person
     */
    public function testSetNotes(Person $person): void
    {
        $notes = "This is a note text.";
        $person->setNotes($notes);

        self::assertEquals($notes, $person->getNotes());
    }
}
