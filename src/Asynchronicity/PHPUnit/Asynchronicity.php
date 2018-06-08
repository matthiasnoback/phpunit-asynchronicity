<?php
declare(strict_types=1);

namespace Asynchronicity\PHPUnit;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * @method static assertThat($value, Constraint $constraint)
 */
trait Asynchronicity
{
    /**
     * Provide a callable. It will be invoked until it doesn't throw an exception anymore, or the given timeout in milliseconds has passed.
     * Before trying again, the process will wait (sleep) for the duration of the optionally provided amount of milliseconds.
     *
     * @param callable $probe
     * @param int $timeoutMilliseconds
     * @param int $waitMilliseconds
     */
    public static function assertEventually(callable $probe, int $timeoutMilliseconds = 5000, int $waitMilliseconds = 500): void
    {
        self::assertThat(
            $probe,
            new Eventually($timeoutMilliseconds, $waitMilliseconds)
        );
    }
}
