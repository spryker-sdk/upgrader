<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Domain\Checks\NotUnique\TransferProperty;
use CodeCompliance\Domain\Service\FilterService;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class TransferPropertyTest extends BaseCodeComplianceCheckTest
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\TransferProperty
     */
    protected TransferProperty $transferProperty;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->transferProperty = new TransferProperty(new FilterService());
    }

    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase();
        $transferPropertyCheck = $this->transferProperty->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $this->transferProperty->getViolations();

        // Assert
        $this->assertCount(2, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertEquals($violation->producedBy(), $transferPropertyCheck->getName());
        }
    }
}