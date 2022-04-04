<?php
// подключаем автолоадинг
require '../vendor/autoload.php';

// стартуем сессию
if( !session_id() ) @session_start();

// подключаем сокращенные имена namespace
use Aura\SqlQuery\QueryFactory;

use App\model\QueryBuilder;

use League\Plates\Engine;

use DI\ContainerBuilder;

use Delight\Auth\Auth;

use JasonGrimes\Paginator;


// создаем объект для работы с внедрением зависимостей
$containerBuilder = new ContainerBuilder();

// вызываем функцию добавления исключений
$containerBuilder->addDefinitions(
    [Engine::class => function()
        {
            return new Engine('../app/views');
        },

        PDO::class => function()
        {
            $driver = "mysql";
            $host = "localhost";
            $database_name = "oop";
            $username = "root";
            $password = "";
            return new PDO("$driver:host=$host;dbname=$database_name","$username","$password");

        },

        Auth::class => function($container)
        {

            return new Auth($container->get('PDO'),null,null,FALSE);
        },
        QueryFactory::class => function()
        {
            return new QueryFactory('mysql');
        },
        Paginator::class => function($container)
        {
            return new Paginator(count($container->get('App\model\QueryBuilder')->getAll("users_information")), 6, $_GET['page'] ?? 1,'?page=(:num)');
        }
    ]);
$container = $containerBuilder->build();


    // создаем объект для ройтинга и вызываем метод добавления роутев указывая метод запроса, запрос и контроллер с экшеном которые будут обрабатывать данный запрос
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/', ['App\controllers\ViewController','show_users_page']);
        $r->addRoute('GET', '/users', ['App\controllers\ViewController','show_users_page']);
        $r->addRoute('POST', '/users', ['App\controllers\AuthorizationController','users']);

        $r->addRoute('GET', '/register', ['App\controllers\ViewController','show_register_page']);
        $r->addRoute('POST', '/register', ['App\controllers\AuthorizationController','register']);

        $r->addRoute('GET', '/verification', ['App\controllers\AuthorizationController','verification']);


        $r->addRoute('GET', '/login', ['App\controllers\ViewController','show_login_page']);
        $r->addRoute('POST', '/login', ['App\controllers\AuthorizationController','login']);

        $r->addRoute('GET', '/logout', ['App\controllers\AuthorizationController','logout']);

        $r->addRoute('GET', '/profile', ['App\controllers\ViewController','show_profile_page']);

        $r->addRoute('GET', '/edit', ['App\controllers\ViewController','show_edit_page']);
        $r->addRoute('POST', '/edit', ['App\controllers\UserController','edit_make']);

        $r->addRoute('GET', '/security', ['App\controllers\ViewController','show_security_page']);
        $r->addRoute('POST', '/security', ['App\controllers\UserController','security_make']);

        $r->addRoute('GET', '/status', ['App\controllers\ViewController','show_status_page']);
        $r->addRoute('POST', '/status', ['App\controllers\UserController','status_make']);

        $r->addRoute('GET', '/images', ['App\controllers\ViewController','show_media_page']);
        $r->addRoute('POST', '/images', ['App\controllers\UserController','images_make']);

        $r->addRoute('GET', '/create_user', ['App\controllers\ViewController','show_create_user_page']);
        $r->addRoute('POST', '/create_user', ['App\controllers\UserController','create_user_make']);

        $r->addRoute('GET', '/delete', ['App\controllers\UserController','delete_user']);
    });

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
            echo '404. такой страницы не существует';
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            // ... 405 Method Not Allowed
            echo '405. не правельный метод запроса';
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            // вызываем метод call указывая контороллер и дополнительные переменные из метода addRoute(если таковые имеются)
            $container->call($routeInfo[1],$routeInfo[2]);
            break;
    }






















?>