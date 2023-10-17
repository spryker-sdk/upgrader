<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\ModuleNameConflictChecker\PreviousProjectModulesStateStorage;

use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ProjectModulesStateDto;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\PreviousProjectModulesStateStorage\PreviousProjectModulesStateStorage;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PreviousProjectModulesStateStorageTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetPreviousProjectModulesStateShouldSetState(): void
    {
        // Arrange
        $state = new ProjectModulesStateDto([], []);
        $storage = new PreviousProjectModulesStateStorage();

        // Act
        $storage->setPreviousProjectModulesState($state);

        // Assert
        $this->assertSame($state, $storage->getPreviousProjectModulesState());
    }

    /**
     * @return void
     */
    public function testGetRequiredProjectModulesStateShouldThrowExceptionWhenValueNotSet(): void
    {
        // Arrange & Assert
        $this->expectException(InvalidArgumentException::class);
        $storage = new PreviousProjectModulesStateStorage();

        // Act
        $storage->getRequiredPreviousProjectModulesState();
    }

    /**
     * @return void
     */
    public function testGetRequiredPreviousProjectModulesStateShouldReturnState(): void
    {
        // Arrange
        $state = new ProjectModulesStateDto([], []);
        $storage = new PreviousProjectModulesStateStorage();
        $storage->setPreviousProjectModulesState($state);

        // Act
        $returnedState = $storage->getRequiredPreviousProjectModulesState();

        // Assert
        $this->assertSame($state, $returnedState);
    }

    /**
     * @return void
     */
    public function testGetPreviousProjectModulesStateShouldReturnState(): void
    {
        // Arrange
        $state = new ProjectModulesStateDto([], []);
        $storage = new PreviousProjectModulesStateStorage();
        $storage->setPreviousProjectModulesState($state);

        // Act
        $returnedState = $storage->getPreviousProjectModulesState();

        // Assert
        $this->assertSame($state, $returnedState);
    }
}
