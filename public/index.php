<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use League\Container\Container;
use PersonRegistry\Config;
use PersonRegistry\Controllers\HomeController;
use PersonRegistry\Repositories\PDORepository;
use PersonRegistry\Repositories\PersonRepository;
use PersonRegistry\Services\PersonService;
use PersonRegistry\Views\TwigView;
use PersonRegistry\Views\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$container = new Container();

$container->add(Config::class, Config::class);
$container->add(PersonRepository::class, PDORepository::class)
    ->addArgument(Config::class);
$container->add(PersonService::class, PersonService::class)
    ->addArgument(PersonRepository::class);

$container->add(FilesystemLoader::class, FilesystemLoader::class)
    ->addArgument(__DIR__ . '/../app/Views/twig');
$container->add(Environment::class, Environment::class)
    ->addArgument(FilesystemLoader::class)
    ->addArgument(
        [
            'cache' => __DIR__ . '/../twig_cache',
            'auto_reload' => true,
        ]
    );
$container->add(View::class, TwigView::class)
    ->addArgument(Environment::class);

$container->add(HomeController::class, HomeController::class)
    ->addArgument(PersonService::class)
    ->addArgument(View::class);


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
