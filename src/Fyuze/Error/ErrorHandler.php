<?php
namespace Fyuze\Error;

use Closure;
use Exception;

class ErrorHandler implements ErrorHandling
{
    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        register_shutdown_function($this->handleFatalError());
        set_error_handler($this->setErrorHandler());
        set_exception_handler($this->setExceptionHandler());

        $this->register('Exception', function ($exception) {
            echo $exception->getMessage();
        });
    }

    /**
     * @param $exception
     * @param Closure $handler
     * @return $this
     */
    public function register($exception, Closure $handler)
    {
        array_unshift($this->handlers, [$exception, $handler]);

        return $this;
    }

    /**
     * @param Exception $exception
     *
     * @return void
     */
    public function handle(Exception $exception)
    {
        try {

            $handlers = array_filter($this->handlers, function ($handler) use ($exception) {
                return $exception instanceof $handler[0];
            });

            foreach ($handlers as $handler) {
                $error = $handler[1]($exception);

                if ($error !== null) {
                    break;
                }
            }

        } catch (Exception $e) {

            echo sprintf('[%d] %s in %s', $e->getLine(), $e->getMessage(), $e->getFile());
        }
    }

    /**
     * @return Closure
     */
    protected function setExceptionHandler()
    {
        return function ($exception) {
            $this->handle($exception);
        };
    }

    /**
     * @return Closure
     */
    protected function setErrorHandler()
    {
        return function ($severity, $message, $file, $line) {
            if (error_reporting() && $severity) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            }

            return true;
        };
    }

    /**
     * @return Closure
     * @codeCoverageIgnore
     */
    protected function handleFatalError()
    {
        return function () {
            $error = error_get_last();

            if ($error['type'] === E_ERROR) {
                $this->handle(new \ErrorException($error['message']));
            }
        };
    }
}
