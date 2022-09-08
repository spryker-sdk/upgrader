<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeComplianceTest\Domain\Checks\Filters;

use CodeCompliance\Domain\Checks\Filters\PersistenceFilter;

class PersistenceFilterTest extends BaseFilterTest
{
    /**
     * @return array<string>
     */
    public function provideClassNamesData(): array
    {
        return [
            'Test/Test/FooClass',
            'Test/Kernel/TestClass',
            'Test/Test/TestEntityManager',
            'Test/Test/TestRepositoryInterface',
            'Test/Test/TestRepository',
            'Test/Test/TestRepositoryInterface',
        ];
    }

    /**
     * @return void
     */
    public function testFilter(): void
    {
        // Arrange
        $businessFactoryFilter = new PersistenceFilter();

        // Act
        $filteredSources = $businessFactoryFilter->filter($this->getCodebaseObjects());

        // Assert
        $this->assertNotEmpty($filteredSources);
        $this->assertCount(2, $filteredSources);
    }
}
