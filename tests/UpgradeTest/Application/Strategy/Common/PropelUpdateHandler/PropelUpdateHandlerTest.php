<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common\PropelUpdateHandler;

use Core\Infrastructure\Service\ProcessRunnerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\PackagePostUpdateHandler\PropelUpdateHandler;
use Upgrade\Domain\Entity\Package;

class PropelUpdateHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsApplicableReturnsTrueWhenReleaseGroupIntegratorEnabled(): void
    {
        // Arrange
        $stepsExecutionDto = new StepsResponseDto(true);
        $stepsExecutionDto->setComposerLockDiff($this->createComposerLockDiffDto());

        $mockProcessRunner = $this->createMock(ProcessRunnerService::class);
        $handler = new PropelUpdateHandler($mockProcessRunner, new Filesystem(), true);

        // Act
        $isApplicable = $handler->isApplicable($stepsExecutionDto);

        // Assert
        $this->assertFalse($isApplicable);
    }

    /**
     * @return void
     */
    public function testIsApplicableReturnsTrueWhenPropelIsInComposerLockDiff(): void
    {
        // Arrange
        $stepsExecutionDto = new StepsResponseDto(true);
        $stepsExecutionDto->setComposerLockDiff($this->createComposerLockDiffDto());

        $mockProcessRunner = $this->createMock(ProcessRunnerService::class);
        $handler = new PropelUpdateHandler($mockProcessRunner, new Filesystem());

        // Act
        $isApplicable = $handler->isApplicable($stepsExecutionDto);

        // Assert
        $this->assertTrue($isApplicable);
    }

    /**
     * @return void
     */
    public function testIsApplicableReturnsFalseWhenPropelIsNotInComposerLockDiff(): void
    {
        // Arrange
        $composerLockDiffDto = new ComposerLockDiffDto([
            new Package('spryker/package', '1.0.1', '1.0.0', 'https://github.com/...'),
        ]);
        $stepsExecutionDto = new StepsResponseDto(true);
        $stepsExecutionDto->setComposerLockDiff($composerLockDiffDto);

        $mockProcessRunner = $this->createMock(ProcessRunnerService::class);
        $handler = new PropelUpdateHandler($mockProcessRunner, new Filesystem());

        // Act
        $isApplicable = $handler->isApplicable($stepsExecutionDto);

        // Assert
        $this->assertFalse($isApplicable);
    }

    /**
     * @return void
     */
    public function testHandle(): void
    {
        // Arrange
        $stepsExecutionDto = new StepsResponseDto(true);
        $stepsExecutionDto->setComposerLockDiff($this->createComposerLockDiffDto());
        $mockProcessRunnerMock = $this->createMock(ProcessRunnerService::class);
        $mockProcessRunnerMock->expects($this->atLeastOnce())->method('run');
        $handler = new PropelUpdateHandler($mockProcessRunnerMock, new Filesystem());

        // Act
        $result = $handler->handle($stepsExecutionDto);

        // Assert
        $this->assertTrue($result->isSuccessful());
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
