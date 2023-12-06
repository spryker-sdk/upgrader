<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\BrokenPhpFilesChecker;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\BrokenPhpFilesChecker;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModuleComparer;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModuleComparerInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesDirsFetcherInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateFetcherInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateStorage;
use PHPUnit\Framework\TestCase;

class BrokenPhpFilesCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckAllShouldReturnEmptyArrayWhenErrorsNotFound(): void
    {
        // Arrange
        $fileErrorsFetcherMock = $this->createFileErrorsFetcherMock([]);
        $checker = new BrokenPhpFilesChecker(
            $fileErrorsFetcherMock,
            new SprykerModuleComparer(),
            new SprykerModulesStateStorage(),
            $this->createSprykerModulesStateFetcherMock(),
            $this->createSprykerModulesDirsFetcherMock(),
        );

        // Act
        $violations = $checker->checkAll();

        // Assert
        $this->assertEmpty($violations);
    }

    /**
     * @return void
     */
    public function testCheckAllShouldReturnViolations(): void
    {
        // Arrange
        $fileErrors = [new FileErrorDto('src/someClass.php', 1, 'error message')];
        $fileErrorsFetcherMock = $this->createFileErrorsFetcherMock($fileErrors);

        $checker = new BrokenPhpFilesChecker(
            $fileErrorsFetcherMock,
            new SprykerModuleComparer(),
            new SprykerModulesStateStorage(),
            $this->createSprykerModulesStateFetcherMock(),
            $this->createSprykerModulesDirsFetcherMock(),
        );

        // Act
        $violations = $checker->checkAll();

        // Assert
        $this->assertCount(1, $violations);
        $this->assertSame($fileErrors, $violations[0]->getFileErrors());
    }

    /**
     * @return void
     */
    public function testCheckUpdatedSprykerModulesShouldReturnNoViolationWhenNoNewPackagesFound(): void
    {
        $previousState = [
            'spryker/module-one' => '1.1.0',
        ];

        $newState = [
            'spryker/module-one' => '1.1.0',
        ];

        // Arrange
        $fileErrorsFetcherMock = $this->createFileErrorsFetcherMock([]);

        $checker = new BrokenPhpFilesChecker(
            $fileErrorsFetcherMock,
            new SprykerModuleComparer(),
            new SprykerModulesStateStorage($previousState),
            $this->createSprykerModulesStateFetcherMock($newState),
            $this->createSprykerModulesDirsFetcherMock(),
        );

        // Act
        $violations = $checker->checkUpdatedSprykerModules([]);

        // Assert
        $this->assertEmpty($violations);
    }

    /**
     * @return void
     */
    public function testCheckUpdatedSprykerModulesShouldReturnNoViolationWhenNoStanErrors(): void
    {
        $previousState = [
            'spryker/module-one' => '1.1.0',
        ];

        $newState = [
            'spryker/module-one' => '1.1.1',
        ];

        // Arrange
        $fileErrorsFetcherMock = $this->createFileErrorsFetcherMock();

        $checker = new BrokenPhpFilesChecker(
            $fileErrorsFetcherMock,
            new SprykerModuleComparer(),
            new SprykerModulesStateStorage($previousState),
            $this->createSprykerModulesStateFetcherMock($newState),
            $this->createSprykerModulesDirsFetcherMock([], ['spryker/module-one']),
        );

        // Act
        $violations = $checker->checkUpdatedSprykerModules([]);

        // Assert
        $this->assertEmpty($violations);
    }

    /**
     * @return void
     */
    public function testCheckUpdatedSprykerModulesShouldReturnViolations(): void
    {
        $previousState = [
            'spryker/module-one' => '1.1.0',
        ];

        $newState = [
            'spryker/module-one' => '1.1.1',
        ];

        // Arrange
        $fileErrors = [new FileErrorDto('src/ModuleOne.php', 1, 'error message')];
        $fileErrorsFetcherMock = $this->createFileErrorsFetcherMock($fileErrors);

        $checker = new BrokenPhpFilesChecker(
            $fileErrorsFetcherMock,
            new SprykerModuleComparer(),
            new SprykerModulesStateStorage($previousState),
            $this->createSprykerModulesStateFetcherMock($newState),
            $this->createSprykerModulesDirsFetcherMock(['src/ModuleOne'], ['spryker/module-one']),
        );

        // Act
        $violations = $checker->checkUpdatedSprykerModules([]);

        // Assert
        $this->assertCount(1, $violations);
        $this->assertSame($fileErrors, $violations[0]->getFileErrors());
    }

    /**
     * @return void
     */
    public function testFetchAndPersistInstalledSprykerModules(): void
    {
        // Arrange
        $storage = new SprykerModulesStateStorage();

        $checker = new BrokenPhpFilesChecker(
            $this->createFileErrorsFetcherMock(),
            new SprykerModuleComparer(),
            $storage,
            $this->createSprykerModulesStateFetcherMock(['spryker/module-one' => '1.1.1']),
            $this->createSprykerModulesDirsFetcherMock(),
        );

        // Act
        $checker->fetchAndPersistInstalledSprykerModules();

        // Assert
        $this->assertSame(['spryker/module-one' => '1.1.1'], $storage->getModulesState());
    }

    /**
     * @return void
     */
    public function testFetchAndPersistInitialErrorsShouldInvokeFetchersMethods(): void
    {
        // Arrange
        $fileErrorsFetcherMock = $this->createMock(FileErrorsFetcherInterface::class);
        $fileErrorsFetcherMock->expects($this->once())->method('reset');
        $fileErrorsFetcherMock->expects($this->once())->method('fetchProjectFileErrorsAndSaveInBaseLine');

        $checker = new BrokenPhpFilesChecker(
            $fileErrorsFetcherMock,
            new SprykerModuleComparer(),
            new SprykerModulesStateStorage(),
            $this->createSprykerModulesStateFetcherMock(),
            $this->createSprykerModulesDirsFetcherMock(),
        );

        // Act
        $checker->fetchAndPersistInitialErrors();
    }

    /**
     * @param array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto> $fileErrors
     *
     * @return \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface
     */
    public function createFileErrorsFetcherMock(array $fileErrors = []): FileErrorsFetcherInterface
    {
        $fileErrorsFetcher = $this->createMock(FileErrorsFetcherInterface::class);
        $fileErrorsFetcher->method('fetchProjectFileErrorsAndSaveInBaseLine')->willReturn($fileErrors);

        return $fileErrorsFetcher;
    }

    /**
     * @param array<mixed> $moduleForUpdate
     *
     * @return \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModuleComparerInterface
     */
    protected function createSprykerModuleComparerMock(array $moduleForUpdate = []): SprykerModuleComparerInterface
    {
        $sprykerModuleComparer = $this->createMock(SprykerModuleComparerInterface::class);
        $sprykerModuleComparer->method('compareForUpdatedModules')->willReturn($moduleForUpdate);

        return $sprykerModuleComparer;
    }

    /**
     * @param array<mixed> $currentModulesState
     *
     * @return \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateFetcherInterface
     */
    protected function createSprykerModulesStateFetcherMock(array $currentModulesState = []): SprykerModulesStateFetcherInterface
    {
        $sprykerModulesStateFetcher = $this->createMock(SprykerModulesStateFetcherInterface::class);
        $sprykerModulesStateFetcher->method('fetchCurrentSprykerModulesState')->willReturn($currentModulesState);

        return $sprykerModulesStateFetcher;
    }

    /**
     * @param array<mixed> $fetchedModulesDirs
     * @param array<string> $expectedPackages
     *
     * @return \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesDirsFetcherInterface
     */
    protected function createSprykerModulesDirsFetcherMock(array $fetchedModulesDirs = [], array $expectedPackages = []): SprykerModulesDirsFetcherInterface
    {
        $sprykerModulesDirsFetcher = $this->createMock(SprykerModulesDirsFetcherInterface::class);
        $sprykerModulesDirsFetcher
            ->expects(count($expectedPackages) > 0 ? $this->once() : $this->never())
            ->method('fetchModulesDirs')->with($expectedPackages)->willReturn($fetchedModulesDirs);

        return $sprykerModulesDirsFetcher;
    }
}
