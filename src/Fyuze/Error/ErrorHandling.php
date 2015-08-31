<?php
namespace Fyuze\Error;

use Closure;
use Exception;

interface ErrorHandling
{
    /**
     * @param $exception
     * @param Closure $handler
     * @return $this
     */
    public function register($exception, Closure $handler);

    /**
     * @param Exception $exception
     * @return mixed
     */
    public function handle(Exception $exception);
}
