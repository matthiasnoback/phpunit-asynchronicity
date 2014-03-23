<?php

namespace Matthias\Polling\Tests;

use Matthias\Polling\CallableProbe;

class CallableProbeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_calls_the_provided_callable_to_determine_if_the_probe_is_satisfied()
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
    public function it_requires_a_valid_callable()
    {
        $this->setExpectedException('\InvalidArgumentException');

        new CallableProbe('not a valid callable');
    }

    /**
     * @test
     */
    public function it_requires_a_boolean_return_value_from_the_callable()
    {
        $callableProbe = new CallableProbe(function () {
            return 'not a boolean value';
        });

        $this->setExpectedException('\InvalidArgumentException');

        $callableProbe->isSatisfied();
    }
}
