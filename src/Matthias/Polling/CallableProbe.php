<?php
declare(strict_types=1);

namespace Matthias\Polling;

use Assert\Assertion;

final class CallableProbe implements Probe
{
    private $callable;

    public function __construct($callable)
    {
        Assertion::true(is_callable($callable), 'Provide a valid callable');
        $this->callable = $callable;
    }

    public function isSatisfied(): bool
    {
        $isSatisfied = call_user_func($this->callable);

        Assertion::boolean($isSatisfied, 'Callable should return a boolean value');

        return $isSatisfied;
    }
}
