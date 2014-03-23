<?php

namespace Matthias\Polling;

use Assert\Assertion;

class Timeout implements TimeoutInterface
{
    private $wait;
    private $clock;
    private $timeout;
    private $timeoutAt;

    /**
     * @param ClockInterface $clock
     * @param integer $wait Wait for n milliseconds
     * @param integer $timeout Timeout after n milliseconds
     */
    public function __construct(ClockInterface $clock, $wait, $timeout)
    {
        Assertion::integer($wait);
        Assertion::integer($timeout);

        $this->clock = $clock;
        $this->wait = static::millisecondsToMicroseconds($wait);
        $this->timeout = static::millisecondsToMicroseconds($timeout);
    }

    public function start()
    {
        $this->timeoutAt = $this->clock->getMicrotime() + $this->timeout;
    }

    private static function millisecondsToMicroseconds($milliseconds)
    {
        return 1000 * $milliseconds;
    }

    public function hasTimedOut()
    {
        if ($this->timeoutAt === null) {
            throw new \LogicException('You need to call start() first');
        }

        $now = $this->clock->getMicrotime();

        return $now >= $this->timeoutAt;
    }

    public function wait()
    {
        usleep($this->wait);
    }
}
