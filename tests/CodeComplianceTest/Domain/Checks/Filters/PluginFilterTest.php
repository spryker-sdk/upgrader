<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeComplianceTest\Domain\Checks\Filters;

use Codebase\Application\Dto\ClassCodebaseDto;
use CodeCompliance\Domain\Checks\Filters\PluginFilter;
use ReflectionClass;

class PluginFilterTest extends BaseFilterTest
{
    /**
     * @return array<string>
     */
    public function provideClassNamesData(): array
    {
        return [
            'TestProject\Method\ProjectPlugin',
            'TestCore\Method\CoreMethod',
        ];
    }

    /**
     * @return void
     */
    public function testFilter(): void
    {
        // Arrange
        $businessFactoryFilter = new PluginFilter();

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
        $dto = new ClassCodebaseDto(['Core']);
        $dto->setClassName($className);
        $dto->setReflection(new ReflectionClass($className));

        return $dto;
    }
}
