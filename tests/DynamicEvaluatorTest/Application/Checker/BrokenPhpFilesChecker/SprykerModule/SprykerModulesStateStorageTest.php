<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\BrokenPhpFilesChecker\SprykerModule;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateStorage;
use PHPUnit\Framework\TestCase;

class SprykerModulesStateStorageTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetAndSetModulesStateShouldReturnValidState(): void
    {
        // Arrange
        $storage = new SprykerModulesStateStorage();
        $state = ['spryker/module' => '1.1.1'];

        // Act
        $storage->setModulesState($state);
        $savedState = $storage->getModulesState();

        // Assert
        $this->assertSame($state, $savedState);
    }
}
