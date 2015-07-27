<?php
namespace Fyuze\Config\Parsers;

use SplFileInfo;

class PHP
{
    /**
     * @var array
     */
    public static $extensions = ['php'];

    /**
     * @param SplFileInfo $file
     * @return array
     */
    public function parse(SplFileInfo $file)
    {
        return require $file->getRealPath();
    }
}
