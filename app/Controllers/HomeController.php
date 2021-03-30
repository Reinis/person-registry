<?php

namespace PersonRegistry\Controllers;

use PersonRegistry\Entities\Person;
use PersonRegistry\Services\PersonService;

class HomeController
{
    private PersonService $service;

    public function __construct(PersonService $service)
    {
        $this->service = $service;
    }

    public function index(): void
    {
        $title = "Person Registry";
        $people = $this->service->getPeople();

        require_once __DIR__ . '/../Views/home.php';
    }

    public function edit(array $vars): void
    {
        $title = "Edit";
        $person = $this->service->getPersonById($vars['id']);

        require_once __DIR__ . '/../Views/edit.php';
    }

    public function update(array $vars): void
    {
        $person = $this->service->getPersonById($vars['id']);
        $notes = $_POST['notes'] ?? '';
        $person->setNotes($notes);

        $this->service->updatePerson($person);

        header('Location: /');
    }

    public function addNew(): void
    {
        $title = "Add New";

        require_once __DIR__ . '/../Views/add.php';
    }

    public function create(array $vars): void
    {
        $firstName = $_POST['first_name'] ?? 'Fnu';
        $lastName = $_POST['last_name'] ?? 'Lnu';
        $nationalId = $_POST['nid'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if (!Person::isValidNationalId($nationalId)) {
            $title = "Invalid person data";
            require_once __DIR__ . '/../Views/error.php';
            die();
        }

        $person = new Person($firstName, $lastName, $nationalId, $notes);
        $this->service->createPerson($person);

        header('Location: /');
    }

    public function delete(array $args): void
    {
        $person = $this->service->getPersonById($args['id']);
        $this->service->deletePerson($person);

        header('Location: /');
    }

    public function search(): void
    {
        $searchField = $_POST['searchField'] ?? 'name';
        $searchTerm = $_POST['searchTerm'] ?? '';

        if ($searchTerm === '') {
            header('Location: /');
        }

        if (!in_array($searchField, ['name', 'nid', 'notes', 'all'])) {
            $title = "Invalid search field";
            require_once __DIR__ . '/../Views/error.php';
            die();
        }

        $title = "Person Registry";
        $people = $this->service->searchForPeople($searchField, $searchTerm);

        require_once __DIR__ . '/../Views/home.php';
    }
}
