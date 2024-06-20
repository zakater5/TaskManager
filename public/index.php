<?php

require '../vendor/autoload.php'; // Inkluda datoteko autoload.php katera nalozi potrebne knjiznice ob zagonu
require '../config.php'; // Inkluda config.php katera poveze in pripravi podatkovno bazo

use FastRoute\RouteCollector; // uporaba knjiznice fastroute za dodelitev zahtev
use function FastRoute\simpleDispatcher; // uporaba funkcije simpleDispacher iz klnjiznice FastRoute

$routes = require __DIR__ . '/../config/routes.php'; // Pridobi datoteko katera specifira katere zahteve gredo kam
$dispatcher = simpleDispatcher($routes); // ustvari nov dispacher objekt

$httpMethod = $_SERVER['REQUEST_METHOD']; // pridobi metodo zahteve npr. GET ali POST
$uri = $_SERVER['REQUEST_URI']; // pridobi endpoint npr. /home, /login

if (false !== $pos = strpos($uri, '?')) { // preveri ali ima zahteva query argument npr. /login?username=urban
    $uri = substr($uri, 0, $pos); // ce ga ima potem loci samo zahtevo od argumentov
}
$uri = rawurldecode($uri); // decod-a url ce ima ta kakrsen % karakter npr. %20 za presledek

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) { // switch statement za status kodo
    case FastRoute\Dispatcher::NOT_FOUND: // ce vsebujo kodo NOT_FOUND potem vrnemo status kodo 404 - not found
        http_response_code(404);
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED: // ce vsebujo kodo NOT_FOUND potem vrnemo status kodo 405 - not allowed
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND: // ce vsebujo kodo FOUND potem use stima in lahko podamo handler, argumente, php klaso/datoteko in povezavo do podatkovne baze
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($class, $method) = explode('@', $handler);

        $controller = new $class($pdo);
        $controller->$method($vars);
        break;
}