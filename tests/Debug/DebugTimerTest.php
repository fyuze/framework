<?php
namespace Debug;

use Fyuze\Debug\Timer;
use PHPUnit\Framework\TestCase;

class DebugTimerTest extends TestCase
{
    /**
     * @dataProvider dataSets
     */
    public function testTimerReturnsAFloatIGuess($name)
    {
        $timer = new Timer();

        $timer->start($name);

        /** @var $start float */
        /** @var $stop float */
        extract($timer->stop($name));

        $this->assertTrue($stop > $start);
    }

    public function testCodeWorksInClosureForSomeReason()
    {
        $timer = new Timer;

        $data = [1, 2];

        $times = $timer->transaction(function () use ($data) {
            for ($i = 0; $i < 1000; $i++) {
                $remove = array_pop($data);
                array_unshift($data, $remove);
            }
        });

        /** @var $start float */
        /** @var $stop float */
        extract($times);

        $this->assertTrue($stop > $start);
        $this->assertEquals(1, count($timer->getTimers()));

    }


    public function dataSets()
    {
        return [['bob', 'marley', 'jojo',' default']];
    }
}
