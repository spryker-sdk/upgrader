<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Domain\Checks\NotUnique\ModuleName;
use CodeCompliance\Domain\Service\FilterService;
use CodeCompliance\Infrastructure\Service\CodeBaseService;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class ModuleNameTest extends BaseCodeComplianceCheckTest
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\ModuleName
     */
    protected ModuleName $moduleName;

    /**
     * @return void
     */
    public function setUp(): void
    {
        /** @var \CodeCompliance\Infrastructure\Service\CodeBaseService $codeBaseService */
        $codeBaseService = static::bootKernel()->getContainer()->get(CodeBaseService::class);
        $this->moduleName = new ModuleName(new FilterService(), $codeBaseService);
    }

    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase();

        $isNotUniqueConstantCheck = $this->moduleName
            ->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $isNotUniqueConstantCheck->getViolations();

        // Assert
        $this->assertCount(8, $violations);

        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertEquals($violation->producedBy(), $isNotUniqueConstantCheck->getName());
        }
    }
}
