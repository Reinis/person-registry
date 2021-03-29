<?php

namespace PersonRegistry\Controllers;

use PersonRegistry\Entities\Person;
use PersonRegistry\Repositories\PersonRepository;

class HomeController
{
    private PersonRepository $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): void
    {
        $people = $this->repository->getPeople();

        $this->header("Person Registry");
        require_once __DIR__ . '/../Views/home.php';
        $this->footer();
    }

    public function header(string $title): void
    {
        require_once __DIR__ . '/../Views/header.php';
    }

    public function footer(): void
    {
        require_once __DIR__ . '/../Views/footer.php';
    }

    public function edit(array $vars): void
    {
        $person = $this->repository->getPersonById($vars['id']);

        $this->header("Edit");
        require_once __DIR__ . '/../Views/edit.php';
        $this->footer();
    }

    public function update(array $vars): void
    {
        $person = $this->repository->getPersonById($vars['id']);
        $notes = $_POST['notes'] ?? '';
        $person->setNotes($notes);

        $this->repository->updatePerson($person);

        header('Location: /');
    }

    public function addNew(): void
    {
        $this->header("Add New");
        require_once __DIR__ . '/../Views/add.php';
        $this->footer();
    }

    public function create(array $vars): void
    {
        $firstName = $_POST['first_name'] ?? 'Fnu';
        $lastName = $_POST['last_name'] ?? 'Lnu';
        $nationalId = $_POST['nid'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if (!Person::isValidNationalId($nationalId)) {
            $this->header("Invalid person data");
            require_once __DIR__ . '/../Views/error.php';
            $this->footer();
            die();
        }

        $person = new Person($firstName, $lastName, $nationalId, $notes);
        $this->repository->createPerson($person);

        header('Location: /');
    }

    public function delete(array $args): void
    {
        $person = $this->repository->getPersonById($args['id']);
        $this->repository->deletePerson($person);

        header('Location: /');
    }
}