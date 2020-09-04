<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

use Exception;

final class Poller
{
    /**
     * Invoke the provided callable until it doesn't throw an exception anymore, or a timeout occurs. The poller will wait before invoking the
     * callable again.
     *
     * @param callable $probe
     * @param Timeout $timeout
     */
    public function poll(callable $probe, Timeout $timeout): void
    {
        $timeout->start();
        $lastException = null;

        while (true) {
            try {
                $returnValue = $probe();

                if ($returnValue !== null) {
                    throw IncorrectUsage::theProbeShouldNotReturnAnything();
                }

                // the probe was successful, so we can return now
                return;
            } catch (IncorrectUsage $exception) {
                throw $exception;
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
