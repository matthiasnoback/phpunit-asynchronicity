<?php
declare(strict_types=1);

namespace Matthias\Polling;

interface TimeoutInterface
{
    public function start();

    public function hasTimedOut(): bool;

    public function wait();
}
