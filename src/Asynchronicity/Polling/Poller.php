<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

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
