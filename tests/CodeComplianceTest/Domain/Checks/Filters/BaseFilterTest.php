<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\Filters;

use Codebase\Application\Dto\ClassCodebaseDto;
use PHPUnit\Framework\TestCase;

abstract class BaseFilterTest extends TestCase
{
    /**
     * @return array<string>
     */
    abstract public function provideClassNamesData(): array;

    /**
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    protected function getCodebaseObjects(): array
    {
        $list = [];

        foreach ($this->provideClassNamesData() as $className) {
            $list[] = $this->createClassCodebaseDtoWithClassName($className);
        }

        return $list;
    }

    /**
     * @param string $className
     *
     * @return \Codebase\Application\Dto\ClassCodebaseDto
     */
    protected function createClassCodebaseDtoWithClassName(string $className): ClassCodebaseDto
    {
        $dto = new ClassCodebaseDto(['Core']);
        $dto->setName($className);

        return $dto;
    }
}
