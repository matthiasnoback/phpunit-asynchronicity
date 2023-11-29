<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TimeoutTest extends TestCase
{
    /**
     * @var Timeout
     */
    private $timeout;

    /**
     * @var MockObject&Clock
     */
    private $clock;

    /**
     * @var int
     */
    private $waitMilliseconds;

    /**
     * @var int
     */
    private $timeoutMilliseconds;

    protected function setUp(): void
    {
        $this->clock = $this->createMock(Clock::class);
        $this->waitMilliseconds = 10;
        $this->timeoutMilliseconds = 50;
        $this->timeout = new Timeout($this->clock, $this->waitMilliseconds, $this->timeoutMilliseconds);
    }

    /**
     * @test
     */
    public function it_times_out_after_a_given_amount_of_milliseconds(): void
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
    public function it_waits_for_the_given_amount_of_milliseconds(): void
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
    public function it_fails_when_start_is_not_called_first(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('start()');

        $this->timeout->hasTimedOut();
    }

    /**
     * @param int[] $microtimes
     */
    private function clockReturnsMicrotimes(array $microtimes): void
    {
        $this->clock
            ->expects($this->exactly(count($microtimes)))
            ->method('getMicrotime')
            ->willReturnOnConsecutiveCalls(...$microtimes);
    }
}
