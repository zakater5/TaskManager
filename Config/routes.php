<?php

use FastRoute\RouteCollector;

return function(RouteCollector $r) { // Preusmeri katere requeste sprejema katera php skripta in katera funkcija v tej skripti
    $r->addRoute('GET', '/', 'App\Controllers\HomeController@index'); // npr. root endpoint bo sprejel HomoController.php s funkcijo index

    $r->addRoute('GET', '/login', 'App\Controllers\auth@login');
    $r->addRoute('POST', '/login', 'App\Controllers\auth@login');
    $r->addRoute('GET', '/logout', 'App\Controllers\auth@logout');

    $r->addRoute('GET', '/get_all_tasks', 'App\Controllers\api@get_all_tasks');
    $r->addRoute('POST', '/add_task', 'App\Controllers\api@add_task');
    $r->addRoute('POST', '/delete_task', 'App\Controllers\api@delete_task');
    $r->addRoute('POST', '/edit_task', 'App\Controllers\api@edit_task');
    $r->addRoute('POST', '/mark_task_complete', 'App\Controllers\api@mark_task_complete');
};
