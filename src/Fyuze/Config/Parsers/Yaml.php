<?php
namespace Fyuze\Config\Parsers;

use SplFileInfo;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
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
     * @throws RuntimeException
     */
    public function parse(SplFileInfo $file)
    {
        try {

            return Parser::parse($file);

        } catch (ParseException $e) {

            throw new RuntimeException(sprintf('Unable to parse yaml in %s - %s', $file->getBasename('.php'), $e->getMessage()));
        }
    }

}
