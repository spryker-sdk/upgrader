<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common\Step;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\Step\IntegratorStep;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Executor\IntegratorExecutor;
use Upgrade\Infrastructure\VersionControlSystem\Git\Adapter\GitAdapter;

class IntegratorStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testIntegratorEnabled(): void
    {
        // Arrange
        $gitAdapter = $this->createMock(GitAdapter::class);

        $integratorExecutor = $this->createMock(IntegratorExecutor::class);
        // Assert
        $integratorExecutor->expects($this->once())->method('runIntegrator');

        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('isIntegratorEnabled')->willReturn(true);

        $integratorStep = new IntegratorStep($gitAdapter, $integratorExecutor, $configurationProvider);

        // Act
        $integratorStep->run(new StepsResponseDto(true));
    }

    /**
     * @return void
     */
    public function testIntegratorDisabled(): void
    {
        // Arrange
        $gitAdapter = $this->createMock(GitAdapter::class);

        $integratorExecutor = $this->createMock(IntegratorExecutor::class);
        // Assert
        $integratorExecutor->expects($this->never())->method('runIntegrator');

        // Arrange
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('isIntegratorEnabled')->willReturn(false);

        $integratorStep = new IntegratorStep($gitAdapter, $integratorExecutor, $configurationProvider);

        // Act
        $integratorStep->run(new StepsResponseDto(true));
    }
}
