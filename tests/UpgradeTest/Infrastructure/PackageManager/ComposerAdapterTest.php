<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\PackageManager;

use PHPUnit\Framework\TestCase;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerCommandExecutorInterface;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerLockComparatorCommandExecutorInterface;
use Upgrade\Infrastructure\PackageManager\ComposerAdapter;
use Upgrade\Infrastructure\PackageManager\Reader\ComposerReaderInterface;

class ComposerAdapterTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsDevPackageShouldReturnValidValue(): void
    {
        // Arrange
        $composerLock = [
            'packages' => [],
            'packages-dev' => [
                [
                    'name' => 'behat/gherkin',
                    'version' => 'v4.8.0',
                ],
            ],
        ];

        $composerAdapter = new ComposerAdapter(
            $this->createMock(ComposerCommandExecutorInterface::class),
            $this->createMock(ComposerLockComparatorCommandExecutorInterface::class),
            $this->createMock(ComposerReaderInterface::class),
            $this->createComposerReaderMock($composerLock),
        );

        // Act
        $result = $composerAdapter->isLockDevPackage('behat/gherkin');

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @param array<mixed> $returnValues
     *
     * @return \Upgrade\Infrastructure\PackageManager\Reader\ComposerReaderInterface
     */
    protected function createComposerReaderMock(array $returnValues): ComposerReaderInterface
    {
        $composerReader = $this->createMock(ComposerReaderInterface::class);
        $composerReader->method('read')->willReturn($returnValues);

        return $composerReader;
    }
}