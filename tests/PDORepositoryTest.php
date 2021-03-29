<?php

namespace PersonRegistryTest;

use InvalidArgumentException;
use PersonRegistry\Config;
use PersonRegistry\Entities\Person;
use PersonRegistry\Repositories\PDORepository;
use PHPUnit\Framework\TestCase;

class PDORepositoryTest extends TestCase
{

    private PDORepository $dataService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->dataService = new PDORepository(new Config(true));
    }

    public function testCreatePerson(): Person
    {
        $firstName = 'Jane';
        $lastName = 'Doe';
        $nationalId = '123456-12346';
        $notes = '';

        $person = new Person($firstName, $lastName, $nationalId, $notes);
        $this->dataService->createPerson($person);
        $retrievedPerson = $this->dataService->getPersonByName($firstName, $lastName);

        self::assertNotEquals(0, $retrievedPerson->getId());
        self::assertEquals($firstName, $retrievedPerson->getFirstName());
        self::assertEquals($lastName, $retrievedPerson->getLastName());
        self::assertEquals($nationalId, $retrievedPerson->getNationalId());
        self::assertEquals($notes, $retrievedPerson->getNotes());

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
        $person->getNationalId();

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
     * @param Person $person
     */
    public function testDeletePerson(Person $person): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->dataService->deletePerson($person);
        $this->dataService->getPersonByName($person->getFirstName(), $person->getLastName());
    }
}
