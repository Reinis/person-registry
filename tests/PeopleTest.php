<?php

namespace PersonRegistryTest;

use InvalidArgumentException;
use PersonRegistry\Entities\Collections\People;
use PersonRegistry\Entities\Person;
use PHPUnit\Framework\TestCase;

class PeopleTest extends TestCase
{
    public function testPeople(): People
    {
        $john = new Person('John', 'Doe', '123456-12345');
        $jane = new Person('Jane', 'Doe', '123456-12346');
        $john->setId(1);
        $jane->setId(2);

        $people = new People($john, $jane);

        self::assertCount(2, $people);
        self::assertTrue($people->hasPerson('123456-12345'));
        self::assertTrue($people->hasPerson('123456-12346'));

        return $people;
    }

    /**
     * @depends testPeople
     * @param People $people
     */
    public function testAddPerson(People $people): void
    {
        $jack = new Person('Jack', 'Doe', '123456-12347');
        $jack->setId(3);

        $people->addPerson($jack);

        self::assertTrue($people->hasPerson('123456-12347'));
    }

    /**
     * @depends testPeople
     * @param People $people
     */
    public function testGetPersonById(People $people): void
    {
        $person = $people->getPersonById(2);

        self::assertEquals('123456-12346', $person->getNationalId());
        self::assertEquals('Jane', $person->getFirstName());
        self::assertEquals('Doe', $person->getLastName());
    }

    /**
     * @depends testPeople
     * @param People $people
     */
    public function testDuplicateIds(People $people): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Duplicate ID 1");

        // The same id as $john from testPeople()
        $mary = new Person('Mary', 'Lu', '123456-54321');
        $mary->setId(1);

        $people->addPerson($mary);
    }

    /**
     * @depends testPeople
     * @param People $people
     */
    public function testDuplicateNIds(People $people): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Person with NID '123456-12345' already exists");

        // The same nid as $john from testPeople()
        $mary = new Person('Mary', 'Lu', '123456-12345');
        $mary->setId(4);

        $people->addPerson($mary);
    }
}
