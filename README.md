# Asynchronicity

[![Build Status](https://travis-ci.org/matthiasnoback/phpunit-asynchronicity.svg?branch=master)](https://travis-ci.org/matthiasnoback/phpunit-asynchronicity)

Using this library you can make a test wait for certain conditions, e.g. to test the output of another process.

See my [blog post on the subject](https://matthiasnoback.nl/2014/03/test-symfony2-commands-using-the-process-component-and-asynchronous-assertions/) for an explanation of the concepts and some code samples. Please note that this article covers version 1 of the library.

# Usage

## With PHPUnit

```php
use Asynchronicity\PHPUnit\Asynchronicity;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class ProcessTest extends TestCase
{
    use Asynchronicity;

    /**
     * @test
     */
    public function it_creates_a_pid_file(): void
    {
        // start the asynchronous process that will eventually create a PID file...  
    
        self::assertEventually(
            function () {
                Assert::assertFileExists(__DIR__ . '/pid');
            }
        );
    }
}
```

## With Behat

Within a Behat `FeatureContext` you could use it for example that a page eventually contains some text:

```php
use Asynchronicity\PHPUnit\Asynchronicity;
use Behat\MinkExtension\Context\MinkContext;
use PHPUnit\Framework\Assert;

final class FeatureContext extends MinkContext
{
    use Asynchronicity;

    /**
     * @Then the stock level has been updated to :expectedStockLevel
     */
    public function thenTheFileHasBeenCreated(string $expectedStockLevel): void
    {
        self::assertEventually(function () use ($expectedStockLevel) {
            $this->visit('/stock-levels');

            $actualStockLevel = $this->getSession()->getPage())->find('css', '.stock-level')->getText();

            Assert::assertEquals($expectedStockLevel, $actualStockLevel);
        });
    }
}
```

## Comments and suggestions

- You can use `$this` inside these callables.
- You can add `use ($...)` to pass in extra data.
- You can throw any type of exception inside the callable to indicate that what you're looking for is not yet the case.
- Often it's convenient to just use the usual assertion methods (PHPUnit or otherwise) inside the callable. They will often provide the right amount of detail in their error messages too.
- `assertEventually()` supports extra arguments for setting the timeout and wait time in milliseconds.
- You can use any callable as the first argument to `assertEventually()`, including objects with an `__invoke()` method or something like `[$object, 'methodName']`.
