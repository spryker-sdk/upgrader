<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeComplianceTest\Domain\Checks\NotUnique;

use CodeCompliance\Configuration\ConfigurationProvider;
use CodeCompliance\Domain\Checks\NotUnique\ModuleName;
use CodeCompliance\Domain\Service\FilterService;
use CodeCompliance\Infrastructure\Adapter\CodeBaseAdapter;
use CodeComplianceTest\Domain\Checks\BaseCodeComplianceCheckTest;

class ModuleNameTest extends BaseCodeComplianceCheckTest
{
    /**
     * @var string
     */
    protected const CORE_MODULE_PATCH = APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Core/CustomModule';

    /**
     * @var string
     */
    protected const PROJECT_MODULE_PATCH = APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Project/Unique/CustomModuleEU';

    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\ModuleName
     */
    protected ModuleName $moduleName;

    /**
     * @return void
     */
    public function setUp(): void
    {
        /** @var \CodeCompliance\Infrastructure\Adapter\CodeBaseAdapter $codeBaseAdapter */
        $codeBaseAdapter = static::bootKernel()->getContainer()->get(CodeBaseAdapter::class);
        $this->moduleName = new ModuleName(new FilterService(), $codeBaseAdapter, new ConfigurationProvider());

        mkdir(static::CORE_MODULE_PATCH, 0777, true);
        mkdir(static::PROJECT_MODULE_PATCH, 0777, true);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        rmdir(static::CORE_MODULE_PATCH);
        rmdir(static::PROJECT_MODULE_PATCH);
    }

    /**
     * @return void
     */
    public function testGetViolations(): void
    {
        // Arrange
        $codebaseSourceDto = $this->readTestCodebase();

        $isNotUniqueConstantCheck = $this->moduleName
            ->setCodebaseSourceDto($codebaseSourceDto);

        // Act
        $violations = $isNotUniqueConstantCheck->getViolations();

        // Assert
        $this->assertCount(7, $violations);

        foreach ($violations as $violation) {
            $this->assertNotEmpty($violation->getId());
            $this->assertNotEmpty($violation->getMessage());
            $this->assertEquals($violation->producedBy(), $isNotUniqueConstantCheck->getName());
        }
    }
}
