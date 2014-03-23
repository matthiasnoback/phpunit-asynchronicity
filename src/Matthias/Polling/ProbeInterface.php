<?php

namespace Matthias\Polling;

interface ProbeInterface
{
    /**
     * Whether or not the probe is satisfied with the current state of the system
     *
     * @return boolean
     */
    public function isSatisfied();
}
