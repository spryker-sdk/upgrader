<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common\Step;

use Core\Infrastructure\Service\ProcessRunnerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\Step\OrmModelsRegenerateStep;
use Upgrade\Domain\Entity\Package;

class OrmModelsRegenerateStepTest extends TestCase
{
 /**
  * @return void
  */
    public function testRunWhenComposerLockDiffExists(): void
    {
        // Arrange
        $stepsExecutionDto = new StepsResponseDto(true);
        $stepsExecutionDto->setComposerLockDiff($this->createComposerLockDiffDto());
        $processRunnerMock = $this->createMock(ProcessRunnerService::class);

        // Assert
        $processRunnerMock->expects($this->atLeastOnce())->method('run');

        // Arrange
        $step = new OrmModelsRegenerateStep($processRunnerMock, new Filesystem());

        // Act
        $step->run($stepsExecutionDto);
    }

    /**
     * @return void
     */
    public function testRunWhenComposerLockDiffNotExists(): void
    {
        // Arrange
        $stepsExecutionDto = new StepsResponseDto(true);
        $processRunnerMock = $this->createMock(ProcessRunnerService::class);

        // Assert
        $processRunnerMock->expects($this->never())->method('run');

        // Arrange
        $step = new OrmModelsRegenerateStep($processRunnerMock, new Filesystem());

        // Act
        $step->run($stepsExecutionDto);
    }

    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto
     */
    public function createComposerLockDiffDto(): ComposerLockDiffDto
    {
        return new ComposerLockDiffDto([
            new Package('propel/propel', '1.0.1', '1.0.0', 'https://github.com/...'),
        ]);
    }
}
