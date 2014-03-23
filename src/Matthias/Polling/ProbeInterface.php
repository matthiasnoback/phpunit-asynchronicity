<?php

namespace Matthias\Polling;

interface ProbeInterface
{
    public function sample();

    public function isSatisfied();
}
