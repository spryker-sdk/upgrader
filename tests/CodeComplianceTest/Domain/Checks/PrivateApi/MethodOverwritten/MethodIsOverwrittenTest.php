<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\PrivateApi\MethodIsOverwritten;

use CodeCompliance\Domain\Checks\PrivateApi\MethodOverwritten\MethodIsOverwritten;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class MethodIsOverwrittenTest extends BaseCodeComplianceCheckTest
{
    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase('MethodIsOverwritten/');

        $methodIsOverwrittenCheck = static::bootKernel()->getContainer()->get(MethodIsOverwritten::class)
            ->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $methodIsOverwrittenCheck->getViolations();

        // Assert
        $this->assertCount(4, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertEquals($violation->producedBy(), $methodIsOverwrittenCheck->getName());
        }
    }
}
