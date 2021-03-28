<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use Dotenv\Dotenv;
use PersonRegistry\Controllers\HomeController;
use PersonRegistry\Repositories\PDORepository;


const DB_DSN = 'PERSON_REGISTRY_DB_DSN';
const DB_USER = 'PERSON_REGISTRY_DB_USER';
const DB_PASSWORD = 'PERSON_REGISTRY_DB_PASSWORD';


$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dotenv->required([DB_DSN, DB_USER, DB_PASSWORD]);

$dsn = $_ENV[DB_DSN];
$user = $_ENV[DB_USER];
$pass = $_ENV[DB_PASSWORD];

$repository = new PDORepository($dsn, $user, $pass);


$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $r) {
        $r->addRoute(['GET', 'POST'], '/', [HomeController::class, 'index']);
        $r->addRoute('GET', '/edit/{id:\d+}', [HomeController::class, 'edit']);
        $r->addRoute('POST', '/edit/{id:\d+}', [HomeController::class, 'update']);
        $r->addRoute('GET', '/add', [HomeController::class, 'addNew']);
        $r->addRoute('POST', '/add', [HomeController::class, 'create']);
        $r->addRoute('POST', '/delete/{id:\d+}', [HomeController::class, 'delete']);
    }
);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];
        $handler = new $class($repository);
        $handler->$method($vars);
        break;
}
