<?php

namespace PersonRegistry\Services;

use PersonRegistry\Entities\Collections\People;
use PersonRegistry\Entities\Person;
use PersonRegistry\Repositories\PersonRepository;

class PersonService
{
    private PersonRepository $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getPeople(): People
    {
        return $this->repository->getPeople();
    }

    public function getPersonById(int $id): Person
    {
        return $this->repository->getPersonById($id);
    }

    public function updatePerson(Person $person): void
    {
        $this->repository->updatePerson($person);
    }

    public function createPerson(Person $person): void
    {
        $this->repository->createPerson($person);
    }

    public function deletePerson(Person $person): void
    {
        $this->repository->deletePerson($person);
    }

    public function searchForPeople(string $searchField, string $searchTerm): People
    {
        switch ($searchField) {
            case 'nid':
                $people = $this->repository->searchByNID($searchTerm);
                break;
            case 'age':
                $people = $this->repository->searchByAge($searchTerm);
                break;
            case 'address':
                $people = $this->repository->searchByAddress($searchTerm);
                break;
            case 'notes':
                $people = $this->repository->searchByNotes($searchTerm);
                break;
            case 'all':
                $people = $this->repository->searchByAll($searchTerm);
                break;
            default:
                $people = $this->repository->searchByName($searchTerm);
                break;
        }

        return $people;
    }
}
