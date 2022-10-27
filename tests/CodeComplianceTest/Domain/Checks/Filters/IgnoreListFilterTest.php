<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

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
            'CoreTest/Test/FooClass', #allows
            'CoreTest/Kernel/TestClass',
            'CoreTest/Development/TestClass',
            'CoreTest/Test/TestBootstrap',
            'CoreTest/Test/TestConfigurationProvider',
            'Spryker/Zed/TestDataImport', #allows
            'Symfony/Component/Validator/Validator/ValidatorInterface',
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
        $this->assertCount(2, $filteredSources);
    }

    /**
     * @param string $className
     *
     * @return \Codebase\Application\Dto\ClassCodebaseDto
     */
    protected function createClassCodebaseDtoWithClassName(string $className): ClassCodebaseDto
    {
        $parent = new ClassCodebaseDto(['Core']);
        $parent->setClassName('Project/Foo/Class');

        $dto = new ClassCodebaseDto(['Core']);
        $dto->setClassName($className);
        $dto->setParent($parent);

        return $dto;
    }
}
