<?php
namespace Fyuze\Log\Handlers;

interface Handler
{
    /**
     * @param $level
     * @param $message
     * @param $context
     * @return mixed
     */
    public function write($level, $message, $context);
}
