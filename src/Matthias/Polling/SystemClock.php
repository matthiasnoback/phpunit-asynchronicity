<?php
declare(strict_types=1);

namespace Matthias\Polling;

final class SystemClock implements Clock
{
    public function getMicrotime(): int
    {
        return (int)round(1000000 * microtime(true));
    }

    public function sleep(int $microseconds): void
    {
        usleep($microseconds);
    }
}
