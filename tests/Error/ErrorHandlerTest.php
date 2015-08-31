<?php

class ErrorHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testHandlesException()
    {
        $handler = new \Fyuze\Error\ErrorHandler();

        ob_start();

        $handler->handle(new Exception('exception handled'));

        $this->assertEquals('exception handled', ob_get_clean());
    }

    public function testHandlesWithRegisteredHandler()
    {
        $handler = new \Fyuze\Error\ErrorHandler();
        $handler->register('TestException', function ($e) {
            echo $e->getMessage();
            return true;
        });

        $message = 'test exception thrown';

        ob_start();

        $handler->handle(new TestException($message));

        $this->assertEquals($message, ob_get_clean());
    }

    public function testCatchesHandlersThrowingErrors()
    {
        $handler = new \Fyuze\Error\ErrorHandler();
        $handler->register('TestException', function ($e) {
            throw new Exception('Oops!');
        });

        ob_start();
        $handler->handle(new TestException('this is just a test'));
        $this->assertTrue(
            strpos(ob_get_clean(), 'Oops!') !== false
        );
    }
}

class TestException extends Exception
{
}
