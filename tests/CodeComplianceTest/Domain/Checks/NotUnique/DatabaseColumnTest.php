<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Domain\Checks\NotUnique\DatabaseColumn;
use CodeCompliance\Domain\Service\FilterService;
use CodeCompliance\Infrastructure\Adapter\CodeBaseReader;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class DatabaseColumnTest extends BaseCodeComplianceCheckTest
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\DatabaseColumn
     */
    protected DatabaseColumn $databaseColumnCheck;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->databaseColumnCheck = new DatabaseColumn(new FilterService(), new CodeBaseReader());
    }

    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase();

        $databaseColumnCheck = $this->databaseColumnCheck->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $this->databaseColumnCheck->getViolations();

        // Assert
        $this->assertCount(2, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertEquals($violation->producedBy(), $databaseColumnCheck->getName());
        }
    }
}
