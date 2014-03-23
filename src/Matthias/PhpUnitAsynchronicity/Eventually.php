<?php

namespace Matthias\PhpUnitAsynchronicity;

use Matthias\Polling\Exception\Interrupted;
use Matthias\Polling\Poller;
use Matthias\Polling\ProbeInterface;
use Matthias\Polling\SystemClock;
use Matthias\Polling\Timeout;

class Eventually extends \PHPUnit_Framework_Constraint
{
    private $timeoutMilliseconds;
    private $waitMilliseconds;

    public function __construct($timeoutMilliseconds = 5000, $waitMilliseconds = 500)
    {
        parent::__construct();

        $this->timeoutMilliseconds = $timeoutMilliseconds;
        $this->waitMilliseconds = $waitMilliseconds;
    }

    public function evaluate($probe, $description = '', $returnResult = false)
    {
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

    public function toString()
    {
        return 'was satisfied within the provided timeout';
    }
}
