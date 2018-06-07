<?php
declare(strict_types=1);

namespace Asynchronicity\Polling;

use PHPUnit\Framework\TestCase;

final class CallableProbeTest extends TestCase
{
    /**
     * @test
     */
    public function it_calls_the_provided_callable_to_determine_if_the_probe_is_satisfied(): void
    {
        $callableProbe = new CallableProbe(function () {
            return true;
        });
        $this->assertTrue($callableProbe->isSatisfied());

        $callableProbe = new CallableProbe(function () {
            return false;
        });
        $this->assertFalse($callableProbe->isSatisfied());
    }

    /**
     * @test
     */
    public function it_requires_a_valid_callable(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CallableProbe('not a valid callable');
    }

    /**
     * @test
     */
    public function it_requires_a_boolean_return_value_from_the_callable(): void
    {
        $callableProbe = new CallableProbe(function () {
            return 'not a boolean value';
        });

        $this->expectException(\InvalidArgumentException::class);

        $callableProbe->isSatisfied();
    }
}
