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

    /**
     * @return void
     */
    public function testGetPackageConstraintShouldReturnNullIfConstraintNotFound(): void
    {
        // Arrange
        $composerJson = [
            'require' => ['spryker/acl' => '^1.2.0'],
            'require-dev' => ['spryker/dev' => '^2.2.0'],
        ];

        $composerAdapter = new ComposerAdapter(
            $this->createMock(ComposerCommandExecutorInterface::class),
            $this->createMock(ComposerLockComparatorCommandExecutorInterface::class),
            $this->createComposerReaderMock($composerJson),
            $this->createMock(ComposerReaderInterface::class),
        );

        // Act
        $result = $composerAdapter->getPackageConstraint('spryker/undefined');

        // Assert
        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function testGetPackageConstraintShouldReturnRequireConstraint(): void
    {
        // Arrange
        $composerJson = [
            'require' => ['spryker/acl' => '^1.2.0'],
            'require-dev' => ['spryker/dev' => '^2.2.0'],
        ];

        $composerAdapter = new ComposerAdapter(
            $this->createMock(ComposerCommandExecutorInterface::class),
            $this->createMock(ComposerLockComparatorCommandExecutorInterface::class),
            $this->createComposerReaderMock($composerJson),
            $this->createMock(ComposerReaderInterface::class),
        );

        // Act
        $result = $composerAdapter->getPackageConstraint('spryker/acl');

        // Assert
        $this->assertSame('^1.2.0', $result);
    }

    /**
     * @return void
     */
    public function testGetPackageConstraintShouldReturnRequireDevConstraint(): void
    {
        // Arrange
        $composerJson = [
            'require' => ['spryker/acl' => '^1.2.0'],
            'require-dev' => ['spryker/dev' => '^2.2.0'],
        ];

        $composerAdapter = new ComposerAdapter(
            $this->createMock(ComposerCommandExecutorInterface::class),
            $this->createMock(ComposerLockComparatorCommandExecutorInterface::class),
            $this->createComposerReaderMock($composerJson),
            $this->createMock(ComposerReaderInterface::class),
        );

        // Act
        $result = $composerAdapter->getPackageConstraint('spryker/dev');

        // Assert
        $this->assertSame('^2.2.0', $result);
    }

    /**
     * @return void
     */
    public function testUpdateLockHashShouldInvokeComposerCommandExecutor(): void
    {
        // Arrange & Assert
        $composerCommandExecutorMock = $this->createMock(ComposerCommandExecutorInterface::class);
        $composerCommandExecutorMock->expects($this->once())->method('updateLockHash');

        $composerAdapter = new ComposerAdapter(
            $composerCommandExecutorMock,
            $this->createMock(ComposerLockComparatorCommandExecutorInterface::class),
            $this->createMock(ComposerReaderInterface::class),
            $this->createMock(ComposerReaderInterface::class),
        );

        // Act
        $composerAdapter->updateLockHash();
    }
}
