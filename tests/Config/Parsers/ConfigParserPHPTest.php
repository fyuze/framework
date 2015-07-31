<?php

class ConfigParserPHPTest extends PHPUnit_Framework_TestCase
{
    public function testParsesPHPFiles()
    {
        $parser = new \Fyuze\Config\Parsers\PHP();

        $path = realpath(__DIR__ . '/../../mocks/resources/config') . '/app-php.php';
        $file = new SplFileInfo($path);
        $config = $parser->parse($file);

        $this->assertArrayHasKey('charset', $config);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionOnInvalidConfig()
    {
        $parser = new \Fyuze\Config\Parsers\PHP();

        $path = realpath(__DIR__ . '/../../mocks/resources/config/invalid/php') . '/app.php';
        $file = new SplFileInfo($path);
        $parser->parse($file);
    }
}
