<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Domain\Checks\NotUnique\DatabaseTable;
use CodeCompliance\Domain\Service\FilterService;
use CodeCompliance\Infrastructure\Adapter\CodeBaseReader;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class DatabaseTableTest extends BaseCodeComplianceCheckTest
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\DatabaseTable
     */
    protected DatabaseTable $databaseTableCheck;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->databaseTableCheck = new DatabaseTable(new FilterService(), new CodeBaseReader());
    }

    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase();
        $databaseTableCheck = $this->databaseTableCheck->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $this->databaseTableCheck->getViolations();

        // Assert
        $this->assertCount(1, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertEquals($violation->producedBy(), $databaseTableCheck->getName());
        }
    }
}
