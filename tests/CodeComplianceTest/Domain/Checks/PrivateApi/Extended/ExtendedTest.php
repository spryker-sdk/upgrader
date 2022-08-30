<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\PrivateApi\Extended;

use CodeCompliance\Domain\Checks\PrivateApi\Extended\Extended;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class ExtendedTest extends BaseCodeComplianceCheckTest
{
    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase('Extended/');

        $extensionCheck = static::bootKernel()->getContainer()->get(Extended::class)->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $extensionCheck->getViolations();

        // Assert
        $this->assertCount(1, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertSame($violation->producedBy(), $extensionCheck->getName());
        }
    }
}
