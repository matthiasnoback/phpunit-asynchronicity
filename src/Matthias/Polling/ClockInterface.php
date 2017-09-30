<?php
declare(strict_types=1);

namespace Matthias\Polling;

interface ClockInterface
{
    public function getMicrotime(): float;
}
