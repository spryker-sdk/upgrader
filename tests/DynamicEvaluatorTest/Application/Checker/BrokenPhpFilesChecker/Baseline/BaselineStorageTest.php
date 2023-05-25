<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\BrokenPhpFilesChecker\Baseline;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline\BaselineStorage;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;
use PHPUnit\Framework\TestCase;

class BaselineStorageTest extends TestCase
{
    /**
     * @return void
     */
    public function testAddFileErrorShouldSkipWhenFileErrorAdded(): void
    {
        // Arrange
        $storage = new BaselineStorage();

        // Act
        $storage->addFileError(new FileErrorDto('somefile.php', 0, 'message'));
        $storage->addFileError(new FileErrorDto('somefile.php', 0, 'message'));

        // Assert
        $this->assertCount(1, $storage->getFileErrors());
        $this->assertSame(['somefile.php' => ['message']], $storage->getFileErrors());
    }

    /**
     * @return void
     */
    public function testHasFileErrorShouldCheckItemAlreadyAdded(): void
    {
        // Arrange
        $storage = new BaselineStorage();

        // Act
        $storage->addFileError(new FileErrorDto('somefile.php', 0, 'message'));
        $hasFileError = $storage->hasFileError(new FileErrorDto('somefile.php', 0, 'message'));

        // Assert
        $this->assertTrue($hasFileError);
    }

    /**
     * @return void
     */
    public function testClearShouldClearStorage(): void
    {
        // Arrange
        $storage = new BaselineStorage();

        // Act
        $storage->addFileError(new FileErrorDto('somefile.php', 0, 'message'));
        $storage->clear();

        // Assert
        $this->assertEmpty($storage->getFileErrors());
    }
}
