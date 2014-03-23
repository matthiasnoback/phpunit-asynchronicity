<?php

namespace Matthias\PhpUnitAsynchronicity\Tests;

use Matthias\Polling\ProbeInterface;

class FileHasBeenCreated implements ProbeInterface
{
    private $fileHasBeenCreated;
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function sample()
    {
        $this->fileHasBeenCreated = is_file($this->path);
    }

    public function isSatisfied()
    {
        if ($this->fileHasBeenCreated === null) {
            return false;
        }

        return $this->fileHasBeenCreated;
    }
}
