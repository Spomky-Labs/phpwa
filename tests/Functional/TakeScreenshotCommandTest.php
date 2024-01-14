<?php

declare(strict_types=1);

namespace SpomkyLabs\PwaBundle\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
final class TakeScreenshotCommandTest extends AbstractPwaTestCase
{
    #[Test]
    public static function aScreenshotIsCorrectlyTake(): void
    {
        // Given
        $command = self::$application->find('pwa:take-screenshot');
        $commandTester = new CommandTester($command);
        $output = sprintf('%s/samples/screenshots/screenshot-1024x1920.png', self::$kernel->getCacheDir());

        // When
        $commandTester->execute([
            'url' => 'https://localhost',
            'output' => $output,
            '--width' => '1024',
            '--height' => '1920',
        ]);

        // Then
        $commandTester->assertCommandIsSuccessful();
        static::assertFileExists($output);
    }
}
