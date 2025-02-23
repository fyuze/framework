<?php

use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
{
    #[WithoutErrorHandler]
    public function testHandlesException()
    {
        $handler = new \Fyuze\Error\ErrorHandler();

        ob_start();

        $handler->handle(new Exception('exception handled'));

        $this->assertSame('exception handled', ob_get_clean());
    }

    #[WithoutErrorHandler]
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

        $this->assertSame($message, ob_get_clean());
    }

    #[WithoutErrorHandler]
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

    #[WithoutErrorHandler]
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
        restore_exception_handler();
        $this->assertSame($message, ob_get_clean());
    }

    #[WithoutErrorHandler]
    public function testSetsErrorHandler()
    {
        $this->expectException(ErrorException::class);

        $handler = new \Fyuze\Error\ErrorHandler();

        $reflect = new ReflectionClass($handler);
        $method = $reflect->getMethod('setErrorHandler');
        $method->setAccessible(true);

        /** @var Closure $closure */
        $closure = $method->invoke($handler);

        $closure(1, '', '', 1);
    }

    #[WithoutErrorHandler]
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
