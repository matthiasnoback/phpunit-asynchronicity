<?php
declare(strict_types=1);

namespace Matthias\PhpUnitAsynchronicity\Tests;

use Matthias\Polling\ProbeInterface;

class FileHasBeenCreated implements ProbeInterface
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function isSatisfied(): bool
    {
        return is_file($this->path);
    }
}
