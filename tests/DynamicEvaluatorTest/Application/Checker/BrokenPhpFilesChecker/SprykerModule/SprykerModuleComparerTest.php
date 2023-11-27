<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\BrokenPhpFilesChecker\SprykerModule;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModuleComparer;
use PHPUnit\Framework\TestCase;

class SprykerModuleComparerTest extends TestCase
{
    /**
     * @return void
     */
    public function testCompareForUpdatedModulesShouldReturnValidModules(): void
    {
        // Arrange
        $previousSprykerModules = [
            'spryker/module-one' => '1.2.0',
            'spryker/module-three' => '1.3.0',
            'spryker/module-four' => '1.4.0',
            'spryker/module-five' => '1.5.0',
        ];

        $newSprykerModules = [
            'spryker/module-one' => '1.3.0',
            'spryker/module-three' => '1.4.0',
            'spryker/module-five' => '1.5.0',
        ];

        $sprykerModuleComparer = new SprykerModuleComparer();

        // Act
        $modules = $sprykerModuleComparer->compareForUpdatedModules($previousSprykerModules, $newSprykerModules);

        // Assert
        $this->assertSame(['spryker/module-one', 'spryker/module-three'], $modules);
    }
}
