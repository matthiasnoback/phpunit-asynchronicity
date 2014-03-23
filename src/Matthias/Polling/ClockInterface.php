<?php

namespace Matthias\Polling;

interface ClockInterface
{
    /**
     * @return float
     */
    public function getMicrotime();
}
