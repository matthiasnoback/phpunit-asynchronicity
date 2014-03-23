<?php

namespace Matthias\Polling\Tests;

use Matthias\Polling\Poller;

class PollerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $probe;

    /**
     * @var Poller
     */
    private $poller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $timeout;

    protected function setUp()
    {
        $this->probe = $this->getMock('Matthias\Polling\ProbeInterface');
        $this->timeout = $this->getMock('Matthias\Polling\TimeoutInterface');
        $this->poller = new Poller();
    }

    /**
     * @test
     */
    public function it_asks_the_probe_if_it_is_satisfied_with_a_sample_until_a_timeout_occurs()
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
    public function it_is_not_interruped_if_no_timeout_occurs_and_the_probe_was_satisfied()
    {
        $this->pollerStartsTimeoutMechanism();

        $this->probeIsSatisfiedAtSecondRun();

        $this->pollerWaits();

        $this->timeoutNeverOccurs();

        $this->poller->poll($this->probe, $this->timeout);
    }

    private function probeIsNeverSatisfied()
    {
        $this->probe
            ->expects($this->atLeastOnce())
            ->method('isSatisfied')
            ->will($this->returnValue(false));
    }

    private function timeoutOccursAtSecondRun()
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

    private function pollerIsInterrupted()
    {
        $this->setExpectedException('Matthias\Polling\Exception\Interrupted');
    }

    private function pollerStartsTimeoutMechanism()
    {
        $this->timeout
            ->expects($this->once())
            ->method('start');
    }

    private function pollerWaits()
    {
        $this->timeout
            ->expects($this->once())
            ->method('wait');
    }

    private function timeoutNeverOccurs()
    {
        $this->timeout
            ->expects($this->any())
            ->method('hasTimedOut')
            ->will($this->returnValue(false));
    }

    private function probeIsSatisfiedAtSecondRun()
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
