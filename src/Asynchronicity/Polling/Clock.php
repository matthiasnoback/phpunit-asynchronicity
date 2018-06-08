<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

interface Clock
{
    /**
     * The current time, in microseconds
     *
     * @return int
     */
    public function getMicrotime(): int;

    /**
     * Sleep (halt the process) for the given number of microseconds
     *
     * @param int $microseconds
     */
    public function sleep(int $microseconds): void;
}
