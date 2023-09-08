<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\Executor;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use PHPUnit\Framework\TestCase;
use ReleaseApp\Application\Configuration\ReleaseAppConstant;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Infrastructure\Executor\IntegratorExecutor;

class IntegratorExecutorTest extends TestCase
{
    /**
     * @return void
     */
    public function testRunIntegratorShouldRunCommandWithoutModules(): void
    {
        // Arrange
        $processRunnerMock = $this->createProcessRunnerServiceMock(
            [APPLICATION_ROOT_DIR . '/vendor/bin/integrator', 'module:manifest:run', '--no-interaction', '--format=json'],
        );
        $integratorExecutor = new IntegratorExecutor($processRunnerMock);
        $stepsExecutionDto = new StepsResponseDto();

        //Act
        $integratorExecutor->runIntegrator($stepsExecutionDto);
    }

    /**
     * @return void
     */
    public function testRunIntegratorShouldRunCommandWithModules(): void
    {
        // Arrange
        $processRunnerMock = $this->createProcessRunnerServiceMock(
            [APPLICATION_ROOT_DIR . '/vendor/bin/integrator', 'module:manifest:run', 'Spryker.Acl:1.0.1,SprykerShop.CompanyPage:2.0.0', '--no-interaction', '--format=json'],
        );
        $integratorExecutor = new IntegratorExecutor($processRunnerMock);
        $stepsExecutionDto = new StepsResponseDto();

        //Act
        $integratorExecutor->runIntegrator(
            $stepsExecutionDto,
            [
                new ModuleDto('spryker/acl', '1.0.1', ReleaseAppConstant::MODULE_TYPE_PATCH),
                new ModuleDto('spryker-shop/company-page', '2.0.0', ReleaseAppConstant::MODULE_TYPE_MAJOR),
            ],
        );
    }

    /**
     * @param array<string> $expectedCommand
     *
     * @return \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected function createProcessRunnerServiceMock(array $expectedCommand): ProcessRunnerServiceInterface
    {
        $processRunnerService = $this->createMock(ProcessRunnerServiceInterface::class);

        $process = $this->createMock(Process::class);
        $process->method('isSuccessful')->willReturn(true);
        $process->method('getOutput')->willReturn('[]');

        $processRunnerService
            ->expects($this->once())
            ->method('run')
            ->with($this->equalTo($expectedCommand))
            ->willReturn($process);

        return $processRunnerService;
    }
}
