<?php
// app/routing.php
use Fyuze\Http\Request;
use Fyuze\Http\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('testRoute', new Route('/', array(
    '_controller' => function () {
        return new Response('Hi');
    }
)));
$collection->add('errorRoute', new Route('/throwD', array(
    '_controller' => function () {
        throw new Exception('error');
    }
)));

return $collection;
