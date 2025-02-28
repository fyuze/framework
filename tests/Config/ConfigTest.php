<?php

use Fyuze\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Setup override
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->config = new Config(realpath(__DIR__ . '/../mocks/app/config'), 'testing');
    }

    public function testConfigParsesFile()
    {
        $this->assertInstanceOf('Fyuze\Config\Config', $this->config);
    }

    public function testConfigGetsKey()
    {
        $this->assertArrayHasKey('name', $this->config->get('app'));
    }

    public function testConfigGetsKeyWithDotNotation()
    {
        $this->assertEquals('Fyuze', $this->config->get('app.name'));
    }

    public function testConfigReturnsDefaultValue()
    {
        $this->assertNull($this->config->get('abc'));
        $this->assertEquals('def', $this->config->get('abc', 'def'));
    }

    public function testConfigSetsValue()
    {
        $this->config->set('abc', 'def');
        $this->assertEquals('def', $this->config->get('abc'));
    }

    public function testConfigSetsValueWithDotNotation()
    {
        $this->config->set('foo.bar', 'baz');
        $this->assertEquals('baz', $this->config->get('foo.bar'));
    }

    public function testConfigInvalidPath()
    {
        $this->expectException(InvalidArgumentException::class);

        new Config('fakepath', 'prod');
    }
}
