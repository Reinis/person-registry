<?php

namespace PersonRegistry\Repositories;

use PersonRegistry\Entities\Collections\People;
use PersonRegistry\Entities\Person;

interface PersonRepository
{
    public function getPersonByName(string $firstName, string $lastName): Person;

    public function getPersonById(int $id): Person;

    public function getPersonByNId(string $nationalId): Person;

    public function updatePerson(Person $person): void;

    public function createPerson(Person $person): void;

    public function deletePerson(Person $person): void;

    public function getPeople(): People;

    public function searchByName(string $searchTerm): People;

    public function searchByNID(string $searchTerm): People;

    public function searchByNotes(string $searchTerm): People;

    public function searchByAll(string $searchTerm): People;
}
