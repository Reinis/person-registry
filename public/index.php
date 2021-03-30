<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use League\Container\Container;
use PersonRegistry\Config;
use PersonRegistry\Controllers\HomeController;
use PersonRegistry\Repositories\PersonRepository;
use PersonRegistry\Repositories\PDORepository;
use PersonRegistry\Services\PersonService;


$container = new Container();

$container->add(Config::class, Config::class);
$container->add(PersonRepository::class, PDORepository::class)
    ->addArgument(Config::class);
$container->add(PersonService::class, PersonService::class)
    ->addArgument(PersonRepository::class);
$container->add(HomeController::class, HomeController::class)
    ->addArgument(PersonService::class);


$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $r) {
        $r->addRoute(['GET', 'POST'], '/', [HomeController::class, 'index']);

        $r->addRoute('GET', '/edit/{id:\d+}', [HomeController::class, 'edit']);
        $r->addRoute('POST', '/edit/{id:\d+}', [HomeController::class, 'update']);

        $r->addRoute('GET', '/add', [HomeController::class, 'addNew']);
        $r->addRoute('POST', '/add', [HomeController::class, 'create']);

        $r->addRoute('POST', '/delete/{id:\d+}', [HomeController::class, 'delete']);

        $r->addRoute(['GET', 'POST'], '/search', [HomeController::class, 'search']);
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
        $container->get($class)->$method($vars);
        break;
}
