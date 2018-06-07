<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

use Assert\Assertion;

final class CallableProbe implements Probe
{
    private $callable;

    public function __construct($callable)
    {
        Assertion::isCallable($callable, 'Provide a valid callable for the probe');
        $this->callable = $callable;
    }

    public function isSatisfied(): bool
    {
        $isSatisfied = \call_user_func($this->callable);

        Assertion::boolean($isSatisfied, 'Callable should return a boolean value');

        return $isSatisfied;
    }
}
