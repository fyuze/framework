<?php
namespace Fyuze\Error\Handlers;

use Exception as BaseException;

class Exception
{
    /**
     * @var BaseException
     */
    protected $exception;

    /**
     * Exception constructor.
     *
     * @param BaseException $exception
     */
    public function __construct(BaseException $exception)
    {
        $this->exception = $exception;
    }

    /**
     *
     */
    public function display()
    {
        echo $this->exception->getMessage();
    }
}
