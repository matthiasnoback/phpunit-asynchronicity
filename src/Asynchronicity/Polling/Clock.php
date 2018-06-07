<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

interface Clock
{
    public function getMicrotime(): int;

    public function sleep(int $microseconds): void;
}
