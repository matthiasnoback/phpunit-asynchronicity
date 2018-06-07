<?php
declare(strict_types=1);

namespace Matthias\Polling;

interface Clock
{
    public function getMicrotime(): float;

    public function sleep(int $microseconds): void;
}
