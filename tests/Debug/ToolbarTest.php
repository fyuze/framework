<?php
namespace Debug;

use Fyuze\Debug\Collectors\Response as ResponseCollector;
use Fyuze\Debug\Toolbar;
use Fyuze\Http\Response;
use PHPUnit\Framework\TestCase;

class ToolbarTest extends TestCase
{
    public function testToolbarRegisteresCollectorsAndRenders()
    {
        $toolbar = new Toolbar();
        $reflection = new \ReflectionClass($toolbar);
        $property = $reflection->getProperty('collectors');
        $property->setAccessible(true);

        $this->assertCount(0, $property->getValue($toolbar));

        $toolbar->addCollector(new ResponseCollector(new Response()));

        $this->assertCount(1, $property->getValue($toolbar));
        $this->assertTrue(strpos((string) $toolbar->render(), 'toolbar') !== false);
    }
}
