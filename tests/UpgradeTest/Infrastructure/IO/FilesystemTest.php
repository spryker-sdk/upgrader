<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\IO;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Upgrade\Infrastructure\IO\Filesystem;

class FilesystemTest extends TestCase
{
    /**
     * @return void
     */
    public function testReadFileShouldThrowExceptionWhenFileNotFound(): void
    {
        // Assert
        $this->expectException(IOException::class);

        // Arrange
        $filesystem = new Filesystem();

        // Act
        $filesystem->readFile(__DIR__ . '/un-existent-file.txt');
    }

    /**
     * @return void
     */
    public function testReadFileShouldReadFile(): void
    {
        // Arrange
        $filesystem = new Filesystem();

        // Act
        $fileContent = $filesystem->readFile(__FILE__);

        // Assert
        $this->assertSame(file_get_contents(__FILE__), $fileContent);
    }

    /**
     * @return void
     */
    public function testScanDirShouldThrowExceptionWhenUnableToReadDir(): void
    {
        // Assert
        $this->expectException(IOException::class);

        // Arrange
        $filesystem = new Filesystem();

        // Act
        $filesystem->scanDir(__FILE__);
    }

    /**
     * @return void
     */
    public function testScanDirShouldReturn(): void
    {
        // Arrange
        $filesystem = new Filesystem();

        // Act
        $dirContent = $filesystem->scanDir(__DIR__);

        // Assert
        $this->assertSame(scandir(__DIR__), $dirContent);
    }
}
