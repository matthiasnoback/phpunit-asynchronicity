<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

interface Probe
{
    /**
     * Whether or not the probe is satisfied with the current state of the system
     */
    public function isSatisfied(): bool;
}
