<?php

namespace Matthias\Polling\Tests;

use Matthias\Polling\SystemClock;
use PHPUnit\Framework\TestCase;

class SystemClockTest extends TestCase
{
    /**
     * @test
     */
    public function it_uses_system_function_microtime_to_retrieve_the_current_microtime()
    {
        $systemClock = new SystemClock();

        $expectedMicrotime = 1000000 * microtime(true);
        $actualMicrotime = $systemClock->getMicrotime();

        $difference = $actualMicrotime - $expectedMicrotime;
        $this->assertTrue($difference < 100);
    }
}
