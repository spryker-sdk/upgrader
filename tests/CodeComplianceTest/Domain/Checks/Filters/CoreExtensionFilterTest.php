<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\Filters;

use CodeCompliance\Domain\Checks\Filters\CoreExtensionFilter;

class CoreExtensionFilterTest extends BaseFilterTest
{
    /**
     * @return array<string>
     */
    public function provideClassNamesData(): array
    {
        return [
            'Test/Test/TestClassName',
            'CoreTest/Test/TestClassBusinessFactory',
        ];
    }

    /**
     * @return void
     */
    public function testFilter(): void
    {
        // Arrange
        $businessFactoryFilter = new CoreExtensionFilter();
        $sources = $this->getCodebaseObjects();
        /** @var \Codebase\Application\Dto\ClassCodebaseDto $firstDto */
        $firstDto = $sources[0] ?? null;
        $secondDto = $sources[1] ?? null;
        if ($firstDto && $secondDto) {
            $firstDto->setParent($secondDto);
        }

        // Act
        $filteredSources = $businessFactoryFilter->filter($sources);

        // Assert
        $this->assertNotEmpty($filteredSources);
        $this->assertCount(1, $filteredSources);
    }
}
