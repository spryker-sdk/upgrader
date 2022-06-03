<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\Filters;

use Codebase\Application\Dto\ClassCodebaseDto;
use CodeCompliance\Domain\Checks\Filters\IgnoreListFilter;

class IgnoreListFilterTest extends BaseFilterTest
{
    /**
     * @return array<string>
     */
    public function provideClassNamesData(): array
    {
        return [
            'CoreTest/Test/FooClass',
            'CoreTest/Kernel/TestClass',
            'CoreTest/Development/TestClass',
            'CoreTest/Test/TestBootstrap',
            'CoreTest/Test/TestConfigurationProvider',
            'Spryker/Zed/TestDataImport',
        ];
    }

    /**
     * @return void
     */
    public function testFilter(): void
    {
        // Arrange
        $businessFactoryFilter = new IgnoreListFilter();

        // Act
        $filteredSources = $businessFactoryFilter->filter($this->getCodebaseObjects());

        // Assert
        $this->assertNotEmpty($filteredSources);
        $this->assertCount(1, $filteredSources);
    }

    /**
     * @param string $className
     *
     * @return \Codebase\Application\Dto\ClassCodebaseDto
     */
    protected function createClassCodebaseDtoWithClassName(string $className): ClassCodebaseDto
    {
        $parent = new ClassCodebaseDto(['Core']);
        $parent->setName($className);

        $dto = new ClassCodebaseDto(['Core']);
        $dto->setName('Project/Foo/Class');
        $dto->setParent($parent);

        return $dto;
    }
}
