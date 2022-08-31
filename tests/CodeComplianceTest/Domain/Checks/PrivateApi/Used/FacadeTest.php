<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\PrivateApi\Used\Facade;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class FacadeTest extends BaseCodeComplianceCheckTest
{
    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase('Used/Facade/');
        $facadeCheck = static::bootKernel()->getContainer()->get(Facade::class)
            ->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $facadeCheck->getViolations();

        // Assert
        $this->assertCount(3, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertSame($violation->producedBy(), $facadeCheck->getName());
        }
    }
}
