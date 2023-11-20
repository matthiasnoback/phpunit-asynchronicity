<?php
declare(strict_types=1);

namespace Asynchronicity\PHPUnit;

use PHPUnit\Framework\TestCase;

final class IntegrationTest extends TestCase
{
    use Asynchronicity;

    /**
     * @test
     */
    public function it_waits_until_a_child_process_does_something(): void
    {
        if (!\extension_loaded('pcntl')) {
            self::markTestSkipped('Requires PCNTL extension');
        }

        $timeoutMilliseconds = 2000;
        $waitMilliseconds = 1000;

        $file = sys_get_temp_dir().'/'.uniqid('phpunit-asynchronicity', true);
        self::assertFileDoesNotExist($file);

        $pid = pcntl_fork();
        if ($pid === -1) {
            $this->fail('Could not create child process');
        } elseif ($pid > 0) {
            // the child process has been created
            self::assertEventually(new FileHasBeenCreated($file), $timeoutMilliseconds, $waitMilliseconds);
            unlink($file);
            pcntl_wait($status);
        } else {
            // we are the child process
            file_put_contents($file, 'test');
            exit;
        }
    }
}
