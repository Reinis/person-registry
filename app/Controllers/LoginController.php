<?php

namespace PersonRegistry\Controllers;

use InvalidArgumentException;
use PersonRegistry\Services\PersonService;
use PersonRegistry\Services\TokenService;
use PersonRegistry\Views\View;

class LoginController
{
    private PersonService $personService;
    private TokenService $tokenService;
    private View $view;

    public function __construct(PersonService $personService, TokenService $tokenService, View $view)
    {
        $this->personService = $personService;
        $this->tokenService = $tokenService;
        $this->view = $view;
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
            $message = "Unknown user '$nid'";

            echo $this->view->render('error', compact('message'));
            die();
        }

        $token = $this->tokenService->getTokenByNationalId($nid);

        echo $this->view->render('login', compact('nid', 'token'));
    }

    public function loginWithToken(): void
    {
        $tokenString = $_GET['token'] ?? 'none';

        if ($tokenString === 'none' || ($token = $this->tokenService->getToken($tokenString)) === null) {
            $message = "Invalid token";

            echo $this->view->render('error', compact('message'));
            die();
        }

        $nid = $token->getNationalId();

        $_SESSION['auth']['nid'] = $nid;
        $this->tokenService->deleteToken($nid);

        header('Location: /');
    }

    public function logout(): void
    {
        session_destroy();

        header('Location: /');
    }

    public function dashboard(): void
    {
        $nid = $_SESSION['auth']['nid'] ?? 'none';

        if ($nid === 'none') {
            header('Location: /login');
        }

        $person = $this->personService->getPersonByNationalId($nid);

        echo $this->view->render('dashboard', compact('person'));
    }
}
