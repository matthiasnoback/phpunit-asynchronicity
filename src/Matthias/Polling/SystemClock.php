<?php
declare(strict_types=1);

namespace Matthias\Polling;

final class SystemClock implements ClockInterface
{
    public function getMicrotime(): float
    {
        return 1000000 * microtime(true);
    }
}
