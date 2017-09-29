<?php

namespace Matthias\PhpUnitAsynchronicity;

use Matthias\Polling\CallableProbe;
use Matthias\Polling\Exception\Interrupted;
use Matthias\Polling\Poller;
use Matthias\Polling\ProbeInterface;
use Matthias\Polling\SystemClock;
use Matthias\Polling\Timeout;
use PHPUnit\Framework\Constraint\Constraint;

class Eventually extends Constraint
{
    private $timeoutMilliseconds;
    private $waitMilliseconds;

    public function __construct($timeoutMilliseconds = 5000, $waitMilliseconds = 500)
    {
        // for compatibility with PHPUnit 4
        $parentConstructor = array('parent', '__construct');
        if (is_callable($parentConstructor)) {
            call_user_func($parentConstructor);
        }

        $this->timeoutMilliseconds = $timeoutMilliseconds;
        $this->waitMilliseconds = $waitMilliseconds;
    }

    public function evaluate($probe, $description = '', $returnResult = false)
    {
        if (is_callable($probe)) {
            $probe = new CallableProbe($probe);
        }

        if (!($probe instanceof ProbeInterface)) {
            throw new \InvalidArgumentException('Expected an instance of ProbeInterface');
        }

        try {
            $poller = new Poller();
            $poller->poll(
                $probe,
                new Timeout(new SystemClock(), $this->waitMilliseconds, $this->timeoutMilliseconds)
            );
        } catch (Interrupted $exception) {
            if ($returnResult) {
                return false;
            } else {
                $this->fail($probe, ($description != '' ? $description . "\n" : '') . 'A timeout has occurred');
            }
        }

        return true;
    }

    protected function failureDescription($other)
    {
        return 'the given probe was satisfied within the provided timeout';
    }

    public function toString()
    {
        throw new \BadMethodCallException();
    }
}
