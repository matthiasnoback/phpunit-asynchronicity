<?php
declare(strict_types=1);

namespace Matthias\Polling;

interface ProbeInterface
{
    /**
     * Whether or not the probe is satisfied with the current state of the system
     */
    public function isSatisfied(): bool;
}
