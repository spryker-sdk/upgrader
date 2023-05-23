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
use PHPUnit\Framework\TestCase;

class BrokenPhpFilesCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckShouldReturnEmptyArrayWhenErrorsNotFound(): void
    {
        // Arrange
        $fileErrorsFetcherMock = $this->createFileErrorsFetcherMock([]);
        $checker = new BrokenPhpFilesChecker($fileErrorsFetcherMock);

        // Act
        $violations = $checker->check(['composer require spryker/package']);

        // Assert
        $this->assertEmpty($violations);
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnViolations(): void
    {
        // Arrange
        $fileErrors = [new FileErrorDto('src/someClass.php', 1, 'error message')];
        $commands = ['composer require spryker/package'];
        $fileErrorsFetcherMock = $this->createFileErrorsFetcherMock($fileErrors);
        $checker = new BrokenPhpFilesChecker($fileErrorsFetcherMock);

        // Act
        $violations = $checker->check($commands);

        // Assert
        $this->assertCount(1, $violations);
        $this->assertSame($commands, $violations[0]->getComposerCommands());
        $this->assertSame($fileErrors, $violations[0]->getFileErrors());
    }

    /**
     * @param array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto> $fileErrors
     *
     * @return \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface
     */
    public function createFileErrorsFetcherMock(array $fileErrors): FileErrorsFetcherInterface
    {
        $fileErrorsFetcher = $this->createMock(FileErrorsFetcherInterface::class);
        $fileErrorsFetcher->method('fetchProjectFileErrorsAndSaveInBaseLine')->willReturn($fileErrors);

        return $fileErrorsFetcher;
    }
}
