<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Configuration\ConfigurationProvider;
use CodeCompliance\Domain\Checks\NotUnique\TransferProperty;
use CodeCompliance\Domain\Service\FilterService;
use CodeCompliance\Infrastructure\Service\CodeBaseService;
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
    protected function setUp(): void
    {
        /** @var \CodeCompliance\Infrastructure\Service\CodeBaseService $codeBaseService */
        $codeBaseService = static::bootKernel()->getContainer()->get(CodeBaseService::class);
        $this->transferProperty = new TransferProperty(new FilterService(), $codeBaseService, new ConfigurationProvider());
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
        $this->assertCount(1, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertSame($violation->producedBy(), $transferPropertyCheck->getName());
        }
    }
}
