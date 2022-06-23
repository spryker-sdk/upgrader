<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Domain\Checks\Filters\PluginFilter;
use CodeCompliance\Domain\Checks\NotUnique\Method;
use CodeCompliance\Domain\Service\FilterService;
use CodeCompliance\Infrastructure\Service\CodeBaseService;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class MethodTest extends BaseCodeComplianceCheckTest
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\Method
     */
    protected Method $methodCheck;

    /**
     * @return void
     */
    public function setUp(): void
    {
        /** @var \CodeCompliance\Infrastructure\Service\CodeBaseService $codeBaseService */
        $codeBaseService = static::bootKernel()->getContainer()->get(CodeBaseService::class);
        $this->methodCheck = new Method(new FilterService([new PluginFilter()]), $codeBaseService);
    }

    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase('Method/');
        $transferNameCheck = $this->methodCheck->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $this->methodCheck->getViolations();

        // Assert
        $this->assertCount(4, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertEquals($violation->producedBy(), $transferNameCheck->getName());
        }
    }
}
