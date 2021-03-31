<?php

namespace PersonRegistry\Controllers;

use PersonRegistry\Entities\Person;
use PersonRegistry\Services\PersonService;
use PersonRegistry\Views\View;

class HomeController
{
    private PersonService $service;
    private View $view;

    public function __construct(PersonService $service, View $view)
    {
        $this->service = $service;
        $this->view = $view;
    }

    public function index(): void
    {
        $context = [
            'people' => $this->service->getPeople(),
        ];

        echo $this->view->render('home', $context);
    }

    public function edit(array $vars): void
    {
        $context = [
            'person' => $this->service->getPersonById($vars['id']),
        ];

        echo $this->view->render('edit', $context);
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
        echo $this->view->render('add');
    }

    public function create(array $vars): void
    {
        $firstName = $_POST['first_name'] ?? 'Fnu';
        $lastName = $_POST['last_name'] ?? 'Lnu';
        $nationalId = $_POST['nid'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if (!Person::isValidNationalId($nationalId)) {
            $context = [
                'message' => 'Invalid person data',
            ];

            echo $this->view->render('error', $context);
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
            $context = [
                'message' => 'Invalid search field',
            ];

            echo $this->view->render('error', $context);
            die();
        }

        $context = [
            'searchField' => $searchField,
            'people' => $this->service->searchForPeople($searchField, $searchTerm),
        ];

        echo $this->view->render('home', $context);
    }
}
