<?php

namespace PersonRegistry\Controllers;

use PersonRegistry\Entities\Person;
use PersonRegistry\Services\PersonService;
use PersonRegistry\Views\View;

class HomeController
{
    private PersonService $personService;
    private View $view;

    public function __construct(PersonService $personService, View $view)
    {
        $this->personService = $personService;
        $this->view = $view;
    }

    public function index(): void
    {
        $people = $this->personService->getPeople();

        echo $this->view->render('home', compact('people'));
    }

    public function edit(array $vars): void
    {
        $person = $this->personService->getPersonById($vars['id']);

        echo $this->view->render('edit', compact('person'));
    }

    public function update(array $vars): void
    {
        $person = $this->personService->getPersonById($vars['id']);
        $notes = $_POST['notes'] ?? '';
        $person->setNotes($notes);

        $this->personService->updatePerson($person);

        header('Location: /');
    }

    public function addNew(): void
    {
        echo $this->view->render('add');
    }

    public function create(): void
    {
        $firstName = $_POST['first_name'] ?? 'Fnu';
        $lastName = $_POST['last_name'] ?? 'Lnu';
        $nationalId = $_POST['nid'] ?? '';
        $age = (int)($_POST['age'] ?? 0);
        $address = $_POST['address'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if (!Person::isValidNationalId($nationalId)) {
            $message = 'Invalid person data';

            echo $this->view->render('error', compact('message'));
            die();
        }

        $person = new Person($firstName, $lastName, $nationalId, $age, $address, $notes);
        $this->personService->createPerson($person);

        header('Location: /');
    }

    public function delete(array $args): void
    {
        $person = $this->personService->getPersonById($args['id']);
        $this->personService->deletePerson($person);

        header('Location: /');
    }

    public function search(): void
    {
        $searchField = $_POST['searchField'] ?? 'name';
        $searchTerm = $_POST['searchTerm'] ?? '';

        if ($searchTerm === '') {
            header('Location: /');
        }

        if (!in_array($searchField, ['name', 'nid', 'notes', 'age', 'address', 'all'])) {
            $message = 'Invalid search field';

            echo $this->view->render('error', compact('message'));
            die();
        }

        $people = $this->personService->searchForPeople($searchField, $searchTerm);

        echo $this->view->render('home', compact('searchField', 'people'));
    }
}
