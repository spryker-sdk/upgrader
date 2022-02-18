<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeComplianceTest\Domain\Checks;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\Service\CodebaseService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseCodeComplianceCheckTest extends KernelTestCase
{
    /**
     * @param string|null $subFolder
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function readTestCodebase(?string $subFolder = null): CodebaseSourceDto
    {
        $codebaseRequestDto = new CodebaseRequestDto(
            [APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Project/' . $subFolder],
            [APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Core/' . $subFolder],
            ['TestCore'],
            'TestProject',
        );

        return static::bootKernel()->getContainer()->get(CodebaseService::class)->parseSource($codebaseRequestDto);
    }
}
