<?php

use Fyuze\View\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testParsesPhpFile()
    {
        $view = new View(__DIR__ . '/../mocks/app/views/test.php');

        $this->assertEquals('Hello, World!', $view->render());
        $this->assertEquals('Hello, World!', (string) $view);
    }

    public function testParsesHtmlWithPHP()
    {
        $view = new View(__DIR__ . '/../mocks/app/views/test.html');

        $this->assertEquals("Hello, World!\nGoodbye!", $view->render());
    }

    public function testParsesParameters()
    {
        $view = new View(__DIR__ . '/../mocks/app/views/params.php', ['user' => 'Matthew']);

        $this->assertEquals('Hello, Matthew', $view->render());
    }

    public function testCatchesExceptionThrownInView()
    {
        $view = new View(__DIR__ . '/../mocks/app/views/error.php');

        $this->assertEquals('you shall not pass', (string) $view);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidView()
    {
        new View('');
    }
}
