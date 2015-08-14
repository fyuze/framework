<?php
// app/routing.php
use Fyuze\Http\Response;

$router->get('', 'testRoute', function () {
    return new Response('Hi');
});
