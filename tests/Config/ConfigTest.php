<?php

use Fyuze\Config\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->configPath = realpath(__DIR__ . '/../mocks/resources/config');
    }

    public function testConfigParsesFile()
    {
        $config = new Config($this->configPath, 'testing');

        // Would be exception otherwise
        $this->assertInstanceOf('Fyuze\Config\Config', $config);
    }

    public function testConfigGetsKey()
    {
        $config = new Config($this->configPath, 'testing');
        $this->assertArrayHasKey('charset', $config->get('app'));
        $this->assertEquals('hello', $config->get('say', 'hello'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConfigInvalidPath()
    {
        new Config('fakepath', 'prod');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testParseInvalidKeyWithoutValue()
    {
        $config = new Config($this->configPath, 'testing');

        $config->get('asdf');
    }
}
