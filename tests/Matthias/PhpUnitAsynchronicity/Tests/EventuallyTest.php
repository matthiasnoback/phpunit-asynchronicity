<?php
declare(strict_types=1);

namespace Matthias\PhpUnitAsynchronicity\Tests;

use Matthias\PhpUnitAsynchronicity\Eventually;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Matthias\Polling\ProbeInterface;

final class EventuallyTest extends TestCase
{
    /**
     * @var Eventually
     */
    private $constraint;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ProbeInterface
     */
    private $probe;

    protected function setUp()
    {
        $this->constraint = new Eventually(100, 50);
        $this->probe = $this->createMock(ProbeInterface::class);
    }

    /**
     * @test
     */
    public function it_fails_when_a_timeout_occurs()
    {
        $this->probeAlwaysFails();

        $this->assertFalse($this->constraint->evaluate($this->probe, '', true));
    }

    /**
     * @test
     */
    public function it_succeeds_when_a_timeout_has_not_occurred_and_the_probe_is_satisfied()
    {
        $this->probeIsSatisfied();

        $this->assertTrue($this->constraint->evaluate($this->probe, '', true));
    }

    /**
     * @test
     */
    public function its_failure_message_contains_the_word_timeout()
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
    public function it_is_possible_to_add_a_specific_failure_message()
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
    public function it_fails_if_something_else_than_a_probe_has_been_provided()
    {
        $constraint = new Eventually();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ProbeInterface');

        $constraint->evaluate(new \stdClass());
    }

    /**
     * @test
     */
    public function when_rendering_the_error_message_it_does_not_try_to_export_the_probe_itself_and_crash()
    {
        $constraint = new Eventually(10, 10);

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("A timeout has occurred\nFailed asserting that the given probe was satisfied within the provided timeout.");

        self::assertThat(function () {
            // never pass
            return false;
        }, $constraint);
    }


    /**
     * @test
     */
    public function it_accepts_a_closure_as_probe()
    {
        $constraint = new Eventually();

        $this->assertTrue($constraint->evaluate(function () {
            return true;
        }));
    }

    private function probeAlwaysFails()
    {
        $this->probe
            ->expects($this->exactly(3))
            ->method('isSatisfied')
            ->will($this->returnValue(false));
    }

    private function probeIsSatisfied()
    {
        $this->probe
            ->expects($this->once())
            ->method('isSatisfied')
            ->will($this->returnValue(true));
    }
}
