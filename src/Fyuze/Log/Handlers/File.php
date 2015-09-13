<?php
namespace Fyuze\Log\Handlers;

class File extends Log implements Handler
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function write(){}
}
