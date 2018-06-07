<?php
declare(strict_types=1);

namespace Matthias\Polling\Tests;

use Matthias\Polling\Clock;
use Matthias\Polling\Interrupted;
use Matthias\Polling\Poller;
use Matthias\Polling\Probe;
use Matthias\Polling\Timeout;
use PHPUnit\Framework\TestCase;

final class PollerTest extends TestCase
{
    private $waitTimeInMilliseconds = 1000;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Probe
     */
    private $probe;

    /**
     * @var Poller
     */
    private $poller;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var Timeout
     */
    private $timeout;

    protected function setUp()
    {
        $this->probe = $this->createMock(Probe::class);
        $this->clock = $this->createMock(Clock::class);
        $this->timeout = new Timeout($this->clock, $this->waitTimeInMilliseconds, 5000);
        $this->poller = new Poller();
    }

    /**
     * @test
     */
    public function it_asks_the_probe_if_it_is_satisfied_with_a_sample_and_waits_if_necessary_until_a_timeout_occurs(): void
    {
        $this->clock->expects($this->any())
            ->method('getMicrotime')
            ->willReturn(
                0, // start time
                1 * 1000000, // 1 second, which is well within the configured timeout of 5000
                10 * 1000000 // 10 seconds have passed, which is beyond the configured timeout of 5000
            );
        $this->clock->expects($this->once())
            ->method('sleep')
            ->with($this->waitTimeInMilliseconds * 1000);

        $this->probeIsNeverSatisfied();

        $this->expectException(Interrupted::class);

        $this->poller->poll($this->probe, $this->timeout);
    }

    /**
     * @test
     */
    public function it_is_not_interrupted_if_no_timeout_occurs_and_the_probe_was_satisfied(): void
    {
        $this->clock->expects($this->any())
            ->method('getMicrotime')
            ->willReturn(
            // start time: 0
                0,
                // next time: 1 second, which is well within the configured timeout of 5000
                1 * 1000000
            );
        $this->clock->expects($this->once())
            ->method('sleep')
            ->with($this->waitTimeInMilliseconds * 1000);

        $this->probeIsSatisfiedAtSecondRun();

        $this->poller->poll($this->probe, $this->timeout);

        // just getting here makes the test successful
        $this->addToAssertionCount(1);
    }

    private function probeIsNeverSatisfied(): void
    {
        $this->probe
            ->expects($this->atLeastOnce())
            ->method('isSatisfied')
            ->will($this->returnValue(false));
    }

    private function probeIsSatisfiedAtSecondRun(): void
    {
        $isSatisfied = [false, true];

        $this->probe
            ->expects($this->any())
            ->method('isSatisfied')
            ->will(
                $this->returnCallback(
                    function () use (&$isSatisfied) {
                        $result = current($isSatisfied);

                        next($isSatisfied);

                        return $result;
                    }
                )
            );
    }
}
