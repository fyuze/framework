<?php
namespace Fyuze\Config\Parsers;

use SplFileInfo;
use RuntimeException;

class PHP
{
    /**
     * @var array
     */
    public static $extensions = ['php'];

    /**
     * @param SplFileInfo $file
     * @return array
     * @throws RuntimeException
     */
    public function parse(SplFileInfo $file)
    {
        $contents = require $file->getRealPath();

        if (!is_array($contents)) {
            throw new RuntimeException(sprintf('The configuration file %s did not return an array', $file->getBasename('.php')));
        }

        return $contents;
    }
}
