<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeComplianceTest\Domain\Checks\Filters;

use CodeCompliance\Domain\Checks\Filters\PrivateApiFilter;

class PrivateApiFilterTest extends BaseFilterTest
{
    /**
     * @return array<string>
     */
    public function provideClassNamesData(): array
    {
        return [
            'Test/Test/FooClass',
            'Test/Test/FooConfig',
            'Test/Test/FooController',
            'Test/Test/FooService',
            'Test/Test/FooClient',
            'Test/Test/FooFacade',
            'Test/Test/FooQueryContainer',
            'Test/Test/FooPluginInterface',
            'Test/Test/FooServiceInterface',
            'Test/Test/FooClientInterface',
            'Test/Test/FooFacadeInterface',
            'Test/Test/FooQueryContainerInterface',
        ];
    }

    /**
     * @return void
     */
    public function testFilter(): void
    {
        // Arrange
        $businessFactoryFilter = new PrivateApiFilter();

        // Act
        $filteredSources = $businessFactoryFilter->filter($this->getCodebaseObjects());

        // Assert
        $this->assertNotEmpty($filteredSources);
        $this->assertCount(1, $filteredSources);
    }
}
