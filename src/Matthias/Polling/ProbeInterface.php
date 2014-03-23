<?php

namespace Matthias\Polling;

interface ProbeInterface
{
    /**
     * Take a snapshot of the system to later determine if you are satisfied
     */
    public function sample();

    /**
     * Whether or not you are satisfied based on the sample data you took
     *
     * @return boolean
     */
    public function isSatisfied();
}
