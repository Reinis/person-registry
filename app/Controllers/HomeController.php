<?php

namespace PersonRegistry\Controllers;

use InvalidArgumentException;
use PersonRegistry\Entities\Person;
use PersonRegistry\Services\PersonService;
use PersonRegistry\Services\TokenService;
use PersonRegistry\Views\View;

class HomeController
{
    private PersonService $service;
    private TokenService $tokenService;
    private View $view;

    public function __construct(PersonService $personService, TokenService $tokenService, View $view)
    {
        $this->service = $personService;
        $this->tokenService = $tokenService;
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
        $age = (int)($_POST['age'] ?? 0);
        $address = $_POST['address'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if (!Person::isValidNationalId($nationalId)) {
            $context = [
                'message' => 'Invalid person data',
            ];

            echo $this->view->render('error', $context);
            die();
        }

        $person = new Person($firstName, $lastName, $nationalId, $age, $address, $notes);
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

        if (!in_array($searchField, ['name', 'nid', 'notes', 'age', 'address', 'all'])) {
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

    public function login(): void
    {
        echo $this->view->render('login');
    }

    public function authenticate(): void
    {
        $nid = $_POST['nid'] ?? 'none';
        $failed = false;

        try {
            $this->tokenService->setToken($nid);
        } catch (InvalidArgumentException $e) {
            $failed = true;
        }

        if ($nid === 'none' || $failed) {
            $message = "Unknown user '{$nid}'";

            echo $this->view->render('error', compact('message'));
            die();
        }

        $token = $this->tokenService->getTokenByNationalId($nid);

        echo $this->view->render('login', compact('nid', 'token'));
    }

    public function loginWithToken(): void
    {
        $token = $_GET['token'] ?? 'none';

        if ($token === 'none') {
            $message = "Invalid token";

            echo $this->view->render('error', compact('message'));
            die();
        }

        $_SESSION['auth']['nid'] = $this->tokenService->getToken($token)->getNationalId();

        header('Location: /');
    }

    public function dashboard(): void
    {
        $nid = $_SESSION['auth']['nid'] ?? 'none';

        if ($nid === 'none') {
            header('Location: /login');
        }

        $person = $this->service->getPersonByNationalId($nid);

        echo $this->view->render('dashboard', compact('person'));
    }

    public function logout(): void
    {
        unset($_SESSION['auth']);

        header('Location: /');
    }
}
