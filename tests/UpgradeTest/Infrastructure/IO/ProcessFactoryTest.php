<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\IO;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Upgrade\Infrastructure\IO\ProcessFactory;

class ProcessFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateFromShellCommandlineShouldCreateProcess(): void
    {
        // Arrange
        $processFactory = new ProcessFactory();

        // Act
        $process = $processFactory->createFromShellCommandline('command');

        // Assert
        $this->assertInstanceOf(Process::class, $process);
        $this->assertSame('command', $process->getCommandLine());
    }
}
