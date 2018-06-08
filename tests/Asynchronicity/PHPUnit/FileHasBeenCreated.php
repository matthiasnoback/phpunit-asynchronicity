<?php
declare(strict_types=1);

namespace Asynchronicity\PHPUnit;

final class FileHasBeenCreated
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function __invoke(): void
    {
        if (!is_file($this->path)) {
            throw new \RuntimeException(sprintf('File %s does not exist', $this->path));
        }
    }
}
