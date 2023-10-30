<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common\Step;

use DateTime;
use PHPUnit\Framework\TestCase;
use ReleaseApp\Application\Configuration\ReleaseAppConstant;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\IntegratorExecutorInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\IntegratorExecutionValidatorInterface;
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

        $integratorExecutorValidator = $this->createMock(IntegratorExecutionValidatorInterface::class);
        $integratorExecutorValidator->method('isIntegratorShouldBeInvoked')->willReturn(true);

        $integratorStep = new IntegratorStep($gitAdapter, $integratorExecutor, $integratorExecutorValidator, $this->createConfigurationProviderMock());

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
        $configurationProvider = $this->createMock(IntegratorExecutionValidatorInterface::class);
        $configurationProvider->method('isIntegratorShouldBeInvoked')->willReturn(false);

        $integratorStep = new IntegratorStep($gitAdapter, $integratorExecutor, $configurationProvider, $this->createConfigurationProviderMock());

        // Act
        $integratorStep->run(new StepsResponseDto(true));
    }

    /**
     * @return void
     */
    public function testRunShouldPassModulesInExecutor(): void
    {
        // Arrange
        $module = new ModuleDto('spryker/acl', '1.1.1', ReleaseAppConstant::MODULE_TYPE_MAJOR);
        $releaseGroup = new ReleaseGroupDto(1, 'RG', new ModuleDtoCollection([$module]), new DateTime(), false, '', 100);
        $stepsResponseDto = new StepsResponseDto(true);
        $stepsResponseDto->addAppliedReleaseGroup($releaseGroup);

        $validator = $this->createMock(IntegratorExecutionValidatorInterface::class);
        $validator->method('isIntegratorShouldBeInvoked')->willReturn(true);

        $integratorStep = new IntegratorStep(
            $this->createMock(GitAdapter::class),
            $this->createIntegratorExecutorMock([$module]),
            $validator,
            $this->createConfigurationProviderMock(1),
        );

        // Act
        $integratorStep->run($stepsResponseDto);
    }

    /**
     * @return void
     */
    public function testRunShouldNotPassModulesInExecutorWhenNotSpecifiedReleaseGroup(): void
    {
        // Arrange
        $module = new ModuleDto('spryker/acl', '1.1.1', ReleaseAppConstant::MODULE_TYPE_MAJOR);
        $releaseGroup = new ReleaseGroupDto(1, 'RG', new ModuleDtoCollection([$module]), new DateTime(), false, '', 100);
        $stepsResponseDto = new StepsResponseDto(true);
        $stepsResponseDto->addAppliedReleaseGroup($releaseGroup);

        $validator = $this->createMock(IntegratorExecutionValidatorInterface::class);
        $validator->method('isIntegratorShouldBeInvoked')->willReturn(true);

        $integratorStep = new IntegratorStep(
            $this->createMock(GitAdapter::class),
            $this->createIntegratorExecutorMock([]),
            $validator,
            $this->createConfigurationProviderMock(),
        );

        // Act
        $integratorStep->run($stepsResponseDto);
    }

    /**
     * @param int|null $releaseGroupId
     *
     * @return \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected function createConfigurationProviderMock(?int $releaseGroupId = null): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getReleaseGroupId')->willReturn($releaseGroupId);

        return $configurationProvider;
    }

    /**
     * @param array $expectedModules
     *
     * @return \Upgrade\Application\Adapter\IntegratorExecutorInterface
     */
    protected function createIntegratorExecutorMock(array $expectedModules): IntegratorExecutorInterface
    {
        $integratorExecutor = $this->createMock(IntegratorExecutorInterface::class);
        $integratorExecutor
            ->expects($this->once())
            ->method('runIntegrator')
            ->with($this->isInstanceOf(StepsResponseDto::class), $this->equalTo($expectedModules));

        return $integratorExecutor;
    }
}
