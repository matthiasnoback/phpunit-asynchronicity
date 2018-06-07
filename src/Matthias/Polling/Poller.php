<?php
declare(strict_types=1);

namespace Matthias\Polling;

use Matthias\Polling\Interrupted;

final class Poller
{
    public function poll(Probe $probe, Timeout $timeout): void
    {
        $timeout->start();

        while (!$probe->isSatisfied()) {
            if ($timeout->hasTimedOut()) {
                throw new Interrupted('A timeout has occurred');
            }

            $timeout->wait();
        }
    }
}
