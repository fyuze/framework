<?php
namespace Fyuze\Log\Handlers;

class File implements Handler
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function write($level, $message, $context) {}
}
