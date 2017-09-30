<?php

use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
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

    public function testSetsExceptionHandler()
    {

        $handler = new \Fyuze\Error\ErrorHandler();

        $reflect = new ReflectionClass($handler);
        $method = $reflect->getMethod('setExceptionHandler');
        $method->setAccessible(true);


        $closure = $method->invoke($handler);

        $message = 'exception handler set';

        ob_start();
        $closure->__invoke(new \Exception($message));
        $this->assertEquals($message, ob_get_clean());
    }

    /**
     * @expectedException ErrorException
     */
    public function testSetsErrorHandler()
    {

        $handler = new \Fyuze\Error\ErrorHandler();

        $reflect = new ReflectionClass($handler);
        $method = $reflect->getMethod('setErrorHandler');
        $method->setAccessible(true);

        /** @var Closure $closure */
        $closure = $method->invoke($handler);

        $closure(1, '', '', 1);
    }


    public function testPHPHandlesErrorIfConditionsAreMet()
    {
        error_reporting(0);
        $handler = new \Fyuze\Error\ErrorHandler();

        $reflect = new ReflectionClass($handler);
        $method = $reflect->getMethod('setErrorHandler');
        $method->setAccessible(true);

        /** @var Closure $closure */
        $closure = $method->invoke($handler);

        $result = $closure(0, '', '', 1);

        $this->assertFalse($result);
    }
}

class TestException extends Exception
{
}
