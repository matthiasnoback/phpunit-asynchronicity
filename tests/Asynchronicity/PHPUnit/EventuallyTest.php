<?php
declare(strict_types=1);

namespace Asynchronicity\PHPUnit;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class EventuallyTest extends TestCase
{
    /**
     * @var Eventually
     */
    private $constraint;

    /**
     * @var callable
     */
    private $probe;

    protected function setUp()
    {
        $this->constraint = new Eventually(100, 50);
    }

    /**
     * @test
     */
    public function it_fails_when_a_timeout_occurs(): void
    {
        $this->probeAlwaysFails();

        $this->assertFalse($this->constraint->evaluate($this->probe, '', true));
    }

    /**
     * @test
     */
    public function it_succeeds_when_a_timeout_has_not_occurred_and_the_probe_is_satisfied(): void
    {
        $this->probeIsSatisfied();

        $this->assertTrue($this->constraint->evaluate($this->probe, '', true));
    }

    /**
     * @test
     */
    public function its_failure_message_contains_the_word_timeout(): void
    {
        $this->probeAlwaysFails();

        try {
            $this->assertFalse($this->constraint->evaluate($this->probe));
            $this->fail('Expected the constraint to fail');
        } catch (ExpectationFailedException $exception) {
            $this->assertContains('timeout', $exception->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_is_possible_to_add_a_specific_failure_message(): void
    {
        $this->probeAlwaysFails();

        $specificMessage = 'Something did not happen';

        try {
            $this->assertFalse($this->constraint->evaluate($this->probe, $specificMessage));
            $this->fail('Expected the constraint to fail');
        } catch (ExpectationFailedException $exception) {
            $this->assertContains($specificMessage, $exception->getMessage());
            $this->assertContains('A timeout has occurred', $exception->getMessage());
        }
    }

    /**
     * @test
     */
    public function when_rendering_the_error_message_it_does_not_try_to_export_the_probe_itself_and_crash(): void
    {
        $this->probeAlwaysFails();
        $constraint = new Eventually(10, 10);

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("A timeout has occurred\nFailed asserting that the given probe was satisfied within the provided timeout.");

        self::assertThat($this->probe, $constraint);
    }


    /**
     * @test
     */
    public function it_accepts_a_closure_as_probe(): void
    {
        $constraint = new Eventually();

        $this->assertTrue($constraint->evaluate(function () {
            return true;
        }));
    }

    private function probeAlwaysFails(): void
    {
        $this->probe = function () {
            throw new \RuntimeException('I am never satisfied');
        };
    }

    private function probeIsSatisfied(): void
    {
        $this->probe = function () {
            // I am satisfied, so I don't throw an exception
        };
    }
}
