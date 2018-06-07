<?php
declare(strict_types=1);

namespace Asynchronicity\PHPUnit;

use Asynchronicity\Polling\Probe;

final class FileHasBeenCreated implements Probe
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function isSatisfied(): bool
    {
        return is_file($this->path);
    }
}
