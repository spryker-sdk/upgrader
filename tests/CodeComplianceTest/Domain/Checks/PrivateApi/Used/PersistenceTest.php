<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\PrivateApi\Used\Persistence;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class PersistenceTest extends BaseCodeComplianceCheckTest
{
    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase('Used/Persistence/');

        $persistenceInBusinessModelCheck = static::bootKernel()->getContainer()->get(Persistence::class)
            ->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $persistenceInBusinessModelCheck->getViolations();

        // Assert
        $this->assertCount(4, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertEquals($violation->producedBy(), $persistenceInBusinessModelCheck->getName());
        }
    }
}
