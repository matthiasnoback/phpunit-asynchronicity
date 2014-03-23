<?php

namespace Matthias\Polling;

class SystemClock implements ClockInterface
{
    public function getMicrotime()
    {
        return 1000000 * microtime(true);
    }
}
