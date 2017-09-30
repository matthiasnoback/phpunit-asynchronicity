<?php
declare(strict_types=1);

namespace Matthias\Polling;

use Matthias\Polling\Exception\Interrupted;

class Poller
{
    public function poll(ProbeInterface $probe, TimeoutInterface $timeout)
    {
        $timeout->start();

        while (!$probe->isSatisfied()) {
            if ($timeout->hasTimedOut()) {
                throw new Interrupted();
            }

            $timeout->wait();
        }
    }
}
