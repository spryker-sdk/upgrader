<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeComplianceTest\Domain\Checks\PrivateApi\MethodIsOverridden;

use CodeCompliance\Domain\Checks\PrivateApi\MethodIsOverridden\MethodIsOverridden;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class MethodIsOverriddenTest extends BaseCodeComplianceCheckTest
{
    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase('MethodIsOverridden/');

        $methodIsOverriddenCheck = static::bootKernel()->getContainer()->get(MethodIsOverridden::class)
            ->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $methodIsOverriddenCheck->getViolations();

        // Assert
        $this->assertCount(1, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertSame($violation->producedBy(), $methodIsOverriddenCheck->getName());
        }
    }
}
