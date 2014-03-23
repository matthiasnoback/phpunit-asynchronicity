<?php

namespace Matthias\Polling;

use Assert\Assertion;

class CallableProbe implements ProbeInterface
{
    private $callable;

    public function __construct($callable)
    {
        Assertion::true(is_callable($callable), 'Provide a valid callable');
        $this->callable = $callable;
    }

    public function isSatisfied()
    {
        $isSatisfied = call_user_func($this->callable);

        Assertion::boolean($isSatisfied, 'Callable should return a boolean value');

        return $isSatisfied;
    }
}
