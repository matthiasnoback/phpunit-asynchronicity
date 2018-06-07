<?php
declare(strict_types=1);

namespace Matthias\Polling;

use Assert\Assertion;

final class Timeout
{
    private $wait;
    private $clock;
    private $timeout;
    private $timeoutAt;

    /**
     * @param Clock $clock
     * @param int $wait Wait for n milliseconds
     * @param int $timeout Timeout after n milliseconds
     */
    public function __construct(Clock $clock, int $wait, int $timeout)
    {
        Assertion::greaterThan($wait, 0, 'Wait time should be greater than 0');
        Assertion::greaterThan($timeout, 0, 'Timeout should be greater than 0, or a timeout will happen immediately');

        $this->clock = $clock;
        $this->wait = static::millisecondsToMicroseconds($wait);
        $this->timeout = static::millisecondsToMicroseconds($timeout);
    }

    public function start()
    {
        $this->timeoutAt = $this->clock->getMicrotime() + $this->timeout;
    }

    private static function millisecondsToMicroseconds(int $milliseconds): int
    {
        return 1000 * $milliseconds;
    }

    public function hasTimedOut(): bool
    {
        if ($this->timeoutAt === null) {
            throw new \LogicException('You need to call start() first');
        }

        $now = $this->clock->getMicrotime();

        return $now >= $this->timeoutAt;
    }

    public function wait()
    {
        $this->clock->sleep($this->wait);
    }
}
