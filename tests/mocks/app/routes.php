<?php
use Fyuze\Http\Response;

$router->get('', 'testRoute', function () {
    return new Response('Hi');
});
$router->get('/throwD', 'errorRoute', function () {
    throw new Exception('error');
});
