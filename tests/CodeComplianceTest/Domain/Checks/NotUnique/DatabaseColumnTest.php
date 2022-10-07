<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Configuration\ConfigurationProvider;
use CodeCompliance\Domain\Checks\NotUnique\DatabaseColumn;
use CodeCompliance\Domain\Service\FilterService;
use CodeCompliance\Infrastructure\Adapter\CodeBaseAdapter;
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
    protected function setUp(): void
    {
        /** @var \CodeCompliance\Infrastructure\Adapter\CodeBaseAdapter $codeBaseService */
        $codeBaseService = static::bootKernel()->getContainer()->get(CodeBaseAdapter::class);
        $this->databaseColumnCheck = new DatabaseColumn(new FilterService(), $codeBaseService, new ConfigurationProvider());
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
        $this->assertCount(1, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertSame($violation->producedBy(), $databaseColumnCheck->getName());
        }
    }
}
