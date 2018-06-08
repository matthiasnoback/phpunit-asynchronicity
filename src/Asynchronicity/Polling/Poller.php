<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

use Exception;

final class Poller
{
    public function poll(callable $probe, Timeout $timeout): void
    {
        $timeout->start();
        $lastException = null;

        while (true) {
            try {
                $probe();

                // the probe was successful, so we can return now
                return;
            } catch (Exception $exception) {
                // the probe was unsuccessful, we remember the last exception
                $lastException = $exception;
            }

            if ($timeout->hasTimedOut()) {
                throw new Interrupted('A timeout has occurred', 0, $lastException);
            }

            // we wait before trying again
            $timeout->wait();
        }
    }
}
