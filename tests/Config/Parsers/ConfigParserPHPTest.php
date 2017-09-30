<?php

use PHPUnit\Framework\TestCase;

class ConfigParserPHPTest extends TestCase
{
    public function testParsesPHPFiles()
    {
        $parser = new \Fyuze\Config\Parsers\PHP();

        $path = __DIR__ . '/../../mocks/app/config/app.php';
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

        $path = realpath(__DIR__ . '/../../mocks/app/config/invalid/php') . '/app.php';
        $file = new SplFileInfo($path);
        $parser->parse($file);
    }
}
