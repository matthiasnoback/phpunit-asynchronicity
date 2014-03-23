<?php

namespace Matthias\Polling;

interface TimeoutInterface
{
    public function start();

    /**
     * @return boolean
     */
    public function hasTimedOut();

    public function wait();
}
