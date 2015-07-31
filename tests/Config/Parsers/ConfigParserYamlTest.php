<?php

class ConfigParserYamlTest extends PHPUnit_Framework_TestCase
{
    public function testParsesYaml()
    {
        $parser = new \Fyuze\Config\Parsers\Yaml();

        $path = realpath(__DIR__ . '/../../mocks/resources/config') . '/app.yml';
        $file = new SplFileInfo($path);
        $config = $parser->parse($file);

        $this->assertArrayHasKey('charset', $config);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionOnInvalidConfig()
    {
        $parser = new \Fyuze\Config\Parsers\Yaml();

        $path = realpath(__DIR__ . '/../../mocks/resources/config/invalid/yaml') . '/app.yml';
        $file = new SplFileInfo($path);
        $contents =$parser->parse($file);

        die(var_dump($contents));
    }
}
