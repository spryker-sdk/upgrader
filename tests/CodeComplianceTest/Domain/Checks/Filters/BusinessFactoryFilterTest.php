<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\Filters;

use CodeCompliance\Domain\Checks\Filters\BusinessFactoryFilter;

class BusinessFactoryFilterTest extends BaseFilterTest
{
    /**
     * @return array<string>
     */
    public function provideClassNamesData(): array
    {
        return [
            'Test/Test/TestClassName',
            'Test/Test/TestClassBusinessFactory',
        ];
    }

    /**
     * @return void
     */
    public function testFilter(): void
    {
        // Arrange
        $businessFactoryFilter = new BusinessFactoryFilter();

        // Act
        $filteredSources = $businessFactoryFilter->filter($this->getCodebaseObjects());

        // Assert
        $this->assertNotEmpty($filteredSources);
        $this->assertCount(1, $filteredSources);

        foreach ($filteredSources as $filteredSource) {
            $this->assertMatchesRegularExpression(BusinessFactoryFilter::PATTERN, $filteredSource->getClassName());
        }
    }
}
