<?php

namespace Matthias\Polling\Tests;

use Matthias\Polling\Timeout;
use PHPUnit\Framework\TestCase;

class TimeoutTest extends TestCase
{
    /**
     * @var Timeout
     */
    private $timeout;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $clock;

    private $waitMilliseconds;

    private $timeoutMilliseconds;

    protected function setUp()
    {
        $this->clock = $this->createMock('Matthias\Polling\ClockInterface');
        $this->waitMilliseconds = 10;
        $this->timeoutMilliseconds = 50;
        $this->timeout = new Timeout($this->clock, $this->waitMilliseconds, $this->timeoutMilliseconds);
    }

    /**
     * @test
     */
    public function it_times_out_after_a_given_amount_of_milliseconds()
    {
        $initialMicrotime = 1000000000;

        $this->clockReturnsMicrotimes(
            array(
                $initialMicrotime,
                // halfway before the timeout should occur
                $initialMicrotime + (($this->timeoutMilliseconds * 1000) / 2),
                // after the timeout should have occurred
                $initialMicrotime + 2 * ($this->timeoutMilliseconds * 1000)
            )
        );

        $this->timeout->start();

        // first time: we are halfway
        $this->assertFalse($this->timeout->hasTimedOut());

        // second time: a timeout has occurred
        $this->assertTrue($this->timeout->hasTimedOut());
    }

    /**
     * @test
     */
    public function it_waits_for_the_given_amount_of_milliseconds()
    {
        $startTime = 1000000 * microtime(true);
        $this->timeout->wait();
        $endTime = 1000000 * microtime(true);

        $difference = $endTime - $startTime;
        $this->assertTrue($difference < 50000);
    }

    /**
     * @test
     */
    public function it_fails_when_start_is_not_called_first()
    {
        $this->expectException('\LogicException', 'start()');

        $this->timeout->hasTimedOut();
    }

    private function clockReturnsMicrotimes(array $microtimes)
    {
        static $at;
        if ($at === null) {
            $at = 0;
        }

        foreach ($microtimes as $microtime) {
            $this->clock
                ->expects($this->at($at))
                ->method('getMicrotime')
                ->will($this->returnValue($microtime));

            $at++;
        }
    }
}
