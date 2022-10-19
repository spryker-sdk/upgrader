<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Configuration\ConfigurationProvider;
use CodeCompliance\Domain\Checks\NotUnique\Constant;
use CodeCompliance\Domain\Service\FilterService;
use CodeCompliance\Infrastructure\Adapter\CodeBaseAdapter;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class ConstantTest extends BaseCodeComplianceCheckTest
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\Constant
     */
    protected Constant $constant;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        /** @var \CodeCompliance\Infrastructure\Adapter\CodeBaseAdapter $codeBaseAdapter */
        $codeBaseAdapter = static::bootKernel()->getContainer()->get(CodeBaseAdapter::class);
        $this->constant = new Constant(new FilterService(), $codeBaseAdapter, new ConfigurationProvider());
    }

    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase();

        $isNotUniqueConstantCheck = $this->constant
            ->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $isNotUniqueConstantCheck->getViolations();

        // Assert

        $this->assertCount(1, $violations);

        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertSame($violation->producedBy(), $isNotUniqueConstantCheck->getName());
        }
    }
}
