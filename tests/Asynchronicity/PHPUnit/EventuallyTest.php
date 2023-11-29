<?php
declare(strict_types=1);

namespace Asynchronicity\PHPUnit;

use Asynchronicity\Polling\Interrupted;
use Asynchronicity\Polling\IncorrectUsage;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class EventuallyTest extends TestCase
{
    private Eventually $constraint;

    /**
     * @var callable
     */
    private $probe;

    protected function setUp(): void
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
        } catch (Interrupted $exception) {
            $this->assertStringContainsString('timeout', $exception->getMessage());
        }
    }

    /**
     * @test
     */
    public function when_rendering_the_error_message_it_does_not_try_to_export_the_probe_itself_and_crash(): void
    {
        $this->probeAlwaysFails();
        $constraint = new Eventually(10, 10);

        $this->expectException(Interrupted::class);
        $this->expectExceptionMessage('A timeout has occurred');

        self::assertThat($this->probe, $constraint);
    }

    /**
     * @test
     */
    public function it_fails_if_the_user_returns_false_from_the_probe(): void
    {
        $this->probeReturnsFalse();

        $constraint = new Eventually(10, 10);

        $this->expectException(IncorrectUsage::class);
        $this->expectExceptionMessage('should not return anything');

        self::assertThat($this->probe, $constraint);
    }

    /**
     * @test
     */
    public function it_accepts_a_closure_as_probe(): void
    {
        $constraint = new Eventually();

        $this->assertTrue($constraint->evaluate(function (): void {
            return;
        }));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_the_probe_is_not_callable(): void
    {
        $constraint = new Eventually();

        $this->expectException(IncorrectUsage::class);
        $constraint->evaluate('foobar');
    }

    /**
     * @test
     */
    public function it_is_stringable(): void
    {
        $constraint = new Eventually();

        $this->assertSame('Eventually', $constraint->toString());
    }

    private function probeAlwaysFails(): void
    {
        $this->probe = function (): void {
            Assert::assertTrue(false, 'I am never satisfied');
        };
    }

    private function probeIsSatisfied(): void
    {
        $this->probe = function (): void {
            // I am always satisfied, so I don't throw an exception
            Assert::assertTrue(true);
        };
    }

    private function probeReturnsFalse(): void
    {
        $this->probe = function (): bool {
            // Incorrect usage; the probe shouldn't return anything.
            return false;
        };
    }
}
