<?php
declare(strict_types=1);

namespace Asynchronicity\PHPUnit;

use PHPUnit\Framework\Assert;

final class FileHasBeenCreated
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function __invoke(): void
    {
        Assert::assertFileExists($this->path);
    }
}
