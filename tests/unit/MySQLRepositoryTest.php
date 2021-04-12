<?php

namespace PersonRegistryTest\unit;

use InvalidArgumentException;
use PersonRegistry\Config;
use PersonRegistry\Entities\Person;
use PersonRegistry\Repositories\MySQLPersonRepository;
use PHPUnit\Framework\TestCase;

class MySQLRepositoryTest extends TestCase
{

    private MySQLPersonRepository $dataService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->dataService = new MySQLPersonRepository(new Config(true));
    }

    public function testCreatePerson(): Person
    {
        $person = new Person('Jane', 'Doe', '123456-12346', 0, '', 'note');
        $this->dataService->createPerson($person);
        $retrievedPerson = $this->dataService->getPersonByName('Jane', 'Doe');

        self::assertNotEquals(0, $retrievedPerson->getId());
        self::assertEquals('Jane', $retrievedPerson->getFirstName());
        self::assertEquals('Doe', $retrievedPerson->getLastName());
        self::assertEquals('123456-12346', $retrievedPerson->getNationalId());
        self::assertEquals('note', $retrievedPerson->getNotes());

        return $retrievedPerson;
    }

    /**
     * @depends testCreatePerson
     * @param Person $person
     * @return Person
     */
    public function testGetPersonByName(Person $person): Person
    {
        $firstName = $person->getFirstName();
        $lastName = $person->getLastName();
        $person = $this->dataService->getPersonByName($firstName, $lastName);

        self::assertEquals($firstName, $person->getFirstName());
        self::assertEquals($lastName, $person->getLastName());

        return $person;
    }

    /**
     * @depends testCreatePerson
     * @param Person $person
     */
    public function testUpdatePerson(Person $person): void
    {
        $notes = $person->getNotes();

        if ($notes === '') {
            $notes = "1";
        } else {
            $notes++;
        }

        $person->setNotes($notes);
        $this->dataService->updatePerson($person);

        $updatedPerson = $this->dataService->getPersonByName($person->getFirstName(), $person->getLastName());

        self::assertEquals($notes, $updatedPerson->getNotes());
    }

    /**
     * @depends testCreatePerson
     * @param Person $person
     */
    public function testGetPersonById(Person $person): void
    {
        $retrievedPerson = $this->dataService->getPersonById($person->getId());

        self::assertEquals($person->getNationalId(), $retrievedPerson->getNationalId());
        self::assertEquals($person->getFirstName(), $retrievedPerson->getFirstName());
        self::assertEquals($person->getLastName(), $retrievedPerson->getLastName());
        self::assertEquals($person->getNotes(), $retrievedPerson->getNotes());
    }

    /**
     * @depends testCreatePerson
     * @param Person $person
     */
    public function testGetPersonByNId(Person $person): void
    {
        $retrievedPerson = $this->dataService->getPersonByNId($person->getNationalId());

        self::assertEquals($person->getNationalId(), $retrievedPerson->getNationalId());
        self::assertEquals($person->getFirstName(), $retrievedPerson->getFirstName());
        self::assertEquals($person->getLastName(), $retrievedPerson->getLastName());
        self::assertEquals($person->getNotes(), $retrievedPerson->getNotes());
    }

    /**
     * @depends testCreatePerson
     * @param Person $person
     */
    public function testGetPeople(Person $person): void
    {
        $people = $this->dataService->getPeople();

        self::assertTrue($people->hasPerson($person->getNationalId()));
        self::assertCount(1, $people);
    }

    /**
     * @depends testCreatePerson
     */
    public function testSearchPersonByName(): void
    {
        $people = $this->dataService->searchByName("Jane");

        self::assertCount(1, $people);
        self::assertTrue($people->hasPerson('123456-12346'));
    }

    /**
     * @depends testCreatePerson
     */
    public function testSearchPersonByNID(): void
    {
        $people = $this->dataService->searchByNID("46");

        self::assertCount(1, $people);
        self::assertTrue($people->hasPerson('123456-12346'));
    }

    /**
     * @depends testCreatePerson
     */
    public function testSearchPersonByNotes(): void
    {
        $people = $this->dataService->searchByNotes("no");

        self::assertCount(1, $people);
        self::assertTrue($people->hasPerson('123456-12346'));
    }

    /**
     * @depends testCreatePerson
     */
    public function testSearchPersonByAll(): void
    {
        $people = $this->dataService->searchByAll("46 0  no");

        self::assertCount(1, $people);
        self::assertTrue($people->hasPerson('123456-12346'));
    }

    /**
     * @depends testCreatePerson
     * @param Person $person
     */
    public function testDeletePerson(Person $person): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->dataService->deletePerson($person);
        $this->dataService->getPersonByName($person->getFirstName(), $person->getLastName());
    }
}
