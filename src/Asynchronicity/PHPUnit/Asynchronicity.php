<?php
declare(strict_types=1);

namespace Asynchronicity\PHPUnit;

use Asynchronicity\Polling\Probe;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * @method static assertThat($value, Constraint $constraint)
 */
trait Asynchronicity
{
    /**
     * @param callable|Probe $probe
     * @param int $timeoutMilliseconds
     * @param int $waitMilliseconds
     */
    public static function assertEventually($probe, int $timeoutMilliseconds = 5000, int $waitMilliseconds = 500): void
    {
        self::assertThat(
            $probe,
            new Eventually($timeoutMilliseconds, $waitMilliseconds)
        );
    }
}
