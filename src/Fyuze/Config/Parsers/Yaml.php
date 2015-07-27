<?php
namespace Fyuze\Config\Parsers;

use SplFileInfo;
use Symfony\Component\Yaml\Yaml as Parser;

class Yaml
{
    /**
     * @var array
     */
    public static $extensions = ['yml', 'yaml'];

    /**
     * @param SplFileInfo $file
     * @return array
     */
    public function parse(SplFileInfo $file)
    {
        return Parser::parse($file);
    }

}
