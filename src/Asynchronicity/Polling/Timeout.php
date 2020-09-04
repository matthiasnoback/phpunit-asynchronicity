<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

use InvalidArgumentException;
use LogicException;

final class Timeout
{
    /**
     * @var int
     */
    private $wait;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var int
     */
    private $timeoutAt;

    /**
     * @param Clock $clock
     * @param int $wait Wait for n milliseconds
     * @param int $timeout Timeout after n milliseconds
     */
    public function __construct(Clock $clock, int $wait, int $timeout)
    {
        if ($wait <= 0) {
            throw new InvalidArgumentException('Wait time should be greater than 0');
        }
        if ($timeout <= 0) {
            throw new InvalidArgumentException('Timeout should be greater than 0, or a timeout will happen immediately');
        }

        $this->clock = $clock;
        $this->wait = static::millisecondsToMicroseconds($wait);
        $this->timeout = static::millisecondsToMicroseconds($timeout);
    }

    public function start(): void
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
            throw new LogicException('You need to call start() first');
        }

        $now = $this->clock->getMicrotime();

        return $now >= $this->timeoutAt;
    }

    public function wait(): void
    {
        $this->clock->sleep($this->wait);
    }
}
