<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

use RuntimeException;

final class IncorrectUsage extends RuntimeException
{
    public static function theProbeShouldNotReturnAnything(): self
    {
        return new self(
            'The callable that you provide as the first argument of `assertEventually()` should not return anything.'
            . 'If it doesn\'t throw an exception, it will be considered successful.'
        );
    }
}
