<?php
declare(strict_types=1);

namespace Matthias\Polling\Tests;

use Matthias\Polling\Exception\Interrupted;
use Matthias\Polling\Poller;
use Matthias\Polling\ProbeInterface;
use Matthias\Polling\TimeoutInterface;
use PHPUnit\Framework\TestCase;

final class PollerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ProbeInterface
     */
    private $probe;

    /**
     * @var Poller
     */
    private $poller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TimeoutInterface
     */
    private $timeout;

    protected function setUp()
    {
        $this->probe = $this->createMock(ProbeInterface::class);
        $this->timeout = $this->createMock(TimeoutInterface::class);
        $this->poller = new Poller();
    }

    /**
     * @test
     */
    public function it_asks_the_probe_if_it_is_satisfied_with_a_sample_until_a_timeout_occurs(): void
    {
        $this->pollerStartsTimeoutMechanism();

        $this->probeIsNeverSatisfied();

        $this->pollerWaits();

        $this->timeoutOccursAtSecondRun();

        $this->pollerIsInterrupted();

        $this->poller->poll($this->probe, $this->timeout);
    }

    /**
     * @test
     */
    public function it_is_not_interrupted_if_no_timeout_occurs_and_the_probe_was_satisfied(): void
    {
        $this->pollerStartsTimeoutMechanism();

        $this->probeIsSatisfiedAtSecondRun();

        $this->pollerWaits();

        $this->timeoutNeverOccurs();

        $this->poller->poll($this->probe, $this->timeout);
    }

    private function probeIsNeverSatisfied(): void
    {
        $this->probe
            ->expects($this->atLeastOnce())
            ->method('isSatisfied')
            ->will($this->returnValue(false));
    }

    private function timeoutOccursAtSecondRun(): void
    {
        $hasTimedOut = array(false, true);

        $this->timeout
            ->expects($this->any())
            ->method('hasTimedOut')
            ->will(
                $this->returnCallback(
                    function () use (&$hasTimedOut) {
                        $result = current($hasTimedOut);
                        next($hasTimedOut);
                        return $result;
                    }
                )
            );
    }

    private function pollerIsInterrupted(): void
    {
        $this->expectException(Interrupted::class);
    }

    private function pollerStartsTimeoutMechanism(): void
    {
        $this->timeout
            ->expects($this->once())
            ->method('start');
    }

    private function pollerWaits(): void
    {
        $this->timeout
            ->expects($this->once())
            ->method('wait');
    }

    private function timeoutNeverOccurs(): void
    {
        $this->timeout
            ->expects($this->any())
            ->method('hasTimedOut')
            ->will($this->returnValue(false));
    }

    private function probeIsSatisfiedAtSecondRun(): void
    {
        $isSatisfied = array(false, true);

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
