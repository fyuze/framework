<?php
// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('test_route', new Route('/', array(
    '_controller' => 'AppBundle:Main:homepage',
)));

return $collection;
