<?php

namespace PersonRegistryTest;

use InvalidArgumentException;
use PersonRegistry\Entities\Person;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testName(): void
    {
        $person = new Person("John", "Doe", "123456-12345");

        self::assertEquals("John", $person->getFirstName());
        self::assertEquals("Doe", $person->getLastName());
        self::assertEquals("John Doe", $person->getName());
    }

    public function testNID(): void
    {
        $person1 = new Person("John", "Doe", "123456-12345");
        $person2 = new Person("John", "Doe", "12345612345");

        self::assertEquals("123456-12345", $person1->getNationalId());
        self::assertEquals("12345612345", $person2->getNationalId());
    }

    public function testInvalidNID(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid National Identification Number");

        new Person("John", "Doe", "123456 12345");
    }

    public function testNotesEmpty(): Person
    {
        $person = new Person("John", "Doe", "123456-12345");

        self::assertEmpty($person->getNotes());

        return $person;
    }

    /**
     * @depends testNotesEmpty
     * @param Person $person
     */
    public function testSetNotes(Person $person): void
    {
        $person->setNotes("This is a note text.");

        self::assertEquals("This is a note text.", $person->getNotes());
        self::assertNotEquals("This is another note.", $person->getNotes());
    }

    /**
     * @depends testNotesEmpty
     * @param Person $person
     */
    public function testSetId(Person $person): void
    {
        self::assertEquals(0, $person->getId());

        $person->setId(17);

        self::assertEquals(17, $person->getId());
    }
}
