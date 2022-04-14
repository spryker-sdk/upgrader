<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodebaseTest\Infrastructure\Service;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Infrastructure\SourceParser\SourceParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

class CodebaseServiceTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testParseSourceWithValidPath(): void
    {
        //Arrange
        $codebaseRequestDto = new CodebaseRequestDto(
            '',
            '',
            [APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Core/'],
            ['TestCore'],
        );
        $codebaseRequestDto->setProjectPaths([APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Project/']);

        //Act
        $codebaseSourceDto = static::bootKernel()->getContainer()->get(SourceParser::class)->parseSource($codebaseRequestDto);

        //Assert
        $projectKey = $codebaseSourceDto->getPhpCodebaseSources()['TestProject\TestClassProjectConstant'] ?? null;
        $this->assertNotEmpty($codebaseSourceDto->getPhpCodebaseSources());
        $this->assertNotNull($projectKey);
        $this->assertInstanceOf('\Codebase\Application\Dto\CodebaseInterface', $projectKey);

        $coreKey = $codebaseSourceDto->getPhpCoreCodebaseSources()['TestCore\TestClassCoreConstant'] ?? null;
        $this->assertNotEmpty($codebaseSourceDto->getPhpCoreCodebaseSources());
        $this->assertNotNull($coreKey);
        $this->assertInstanceOf('\Codebase\Application\Dto\CodebaseInterface', $coreKey);
    }

    /**
     * @return void
     */
    public function testParseSourceWithInvalidPath(): void
    {
        //Assert
        $this->expectException(DirectoryNotFoundException::class);

        //Arrange
        $codebaseRequestDto = new CodebaseRequestDto(
            APPLICATION_ROOT_DIR . '/invalidPath/',
            '',
            [APPLICATION_ROOT_DIR . '/invalidPath/'],
        );

        //Act
        static::bootKernel()->getContainer()->get(SourceParser::class)->parseSource($codebaseRequestDto);
    }
}
