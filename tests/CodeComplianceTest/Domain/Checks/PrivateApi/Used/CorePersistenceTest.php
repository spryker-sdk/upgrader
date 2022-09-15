<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\PrivateApi\Used\CorePersistence;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class CorePersistenceTest extends BaseCodeComplianceCheckTest
{
    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase('Used/CorePersistence/');
        $corePersistenceCheck = static::bootKernel()->getContainer()->get(CorePersistence::class)
            ->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $corePersistenceCheck->getViolations();

        // Assert
        $this->assertCount(1, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertSame($violation->producedBy(), $corePersistenceCheck->getName());
        }
    }
}
