<?php

namespace Matthias\PhpUnitAsynchronicity\Tests;

use Matthias\PhpUnitAsynchronicity\Eventually;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_waits_until_a_child_process_does_something()
    {
        $timeoutMilliseconds = 2000;
        $waitMilliseconds = 1000;

        $file = sys_get_temp_dir().'/'.uniqid();
        $this->assertFalse(file_exists($file));

        $pid = pcntl_fork();
        if ($pid === -1) {
            $this->fail('Could not create child process');
        } elseif ($pid > 0) {
            // the child process has been created
            self::assertThat(
                new FileHasBeenCreated($file),
                new Eventually($timeoutMilliseconds, $waitMilliseconds)
            );
            unlink($file);
            pcntl_wait($status);
        } else {
            // we are the child process
            sleep(1);
            touch($file);
            exit;
        }
    }
}
