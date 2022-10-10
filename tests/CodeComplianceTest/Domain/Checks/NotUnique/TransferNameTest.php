<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Configuration\ConfigurationProvider;
use CodeCompliance\Domain\Checks\NotUnique\TransferName;
use CodeCompliance\Domain\Service\FilterService;
use CodeCompliance\Infrastructure\Adapter\CodeBaseAdapter;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class TransferNameTest extends BaseCodeComplianceCheckTest
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\TransferName
     */
    protected TransferName $transferNameCheck;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        /** @var \CodeCompliance\Infrastructure\Adapter\CodeBaseAdapter $codeBaseAdapter */
        $codeBaseAdapter = static::bootKernel()->getContainer()->get(CodeBaseAdapter::class);
        $this->transferNameCheck = new TransferName(new FilterService(), $codeBaseAdapter, new ConfigurationProvider());
    }

    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase();
        $transferNameCheck = $this->transferNameCheck->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $this->transferNameCheck->getViolations();

        // Assert
        $this->assertCount(1, $violations);
        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertSame($violation->producedBy(), $transferNameCheck->getName());
        }
    }
}
