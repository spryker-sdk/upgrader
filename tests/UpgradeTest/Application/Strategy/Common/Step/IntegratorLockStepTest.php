<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace tests\UpgradeTest\Application\Strategy\Common\Step;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\Step\IntegratorLockStep;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Executor\IntegratorExecutor;
use Upgrade\Infrastructure\VersionControlSystem\Git\Adapter\GitAdapter;

class IntegratorLockStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testIntegrator(): void
    {
        // Arrange
        $gitAdapter = $this->createMock(GitAdapter::class);
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $integratorExecutor = $this->createMock(IntegratorExecutor::class);

        // Assert
        $integratorExecutor->expects($this->once())->method('runIntegratorLockUpdater');
        $configurationProvider->expects($this->once())->method('isIntegratorEnabled')->willReturn(true);

        $integratorStep = new IntegratorLockStep($gitAdapter, $integratorExecutor, $configurationProvider);

        // Act
        $integratorStep->run(new StepsResponseDto(true));
    }

    /**
     * @return void
     */
    public function testRollBack(): void
    {
        // Arrange
        $gitAdapter = $this->createMock(GitAdapter::class);
        $integratorExecutor = $this->createMock(IntegratorExecutor::class);
        $configurationProvider = $this->createMock(ConfigurationProvider::class);

        // Assert
        $gitAdapter->expects($this->once())->method('restore');
        $integratorStep = new IntegratorLockStep($gitAdapter, $integratorExecutor, $configurationProvider);

        // Act
        $integratorStep->rollBack(new StepsResponseDto(true));
    }
}
