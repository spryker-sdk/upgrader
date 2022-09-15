<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodebaseTest\Infrastructure\Service;

use Codebase\Application\Dto\SourceParserRequestDto;
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
        $codebaseRequestDto = new SourceParserRequestDto(
            [APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Project/'],
            [APPLICATION_ROOT_DIR . '/tests/data/Evaluate/Core/'],
            ['TestCore'],
            [],
        );

        //Act
        $codebaseSourceDto = static::bootKernel()->getContainer()->get(SourceParser::class)->parseSource($codebaseRequestDto);

        //Assert
        $projectKey = $codebaseSourceDto->getPhpCodebaseSources()['TestProject\TestClassProjectConstant'] ?? null;
        $this->assertNotEmpty($codebaseSourceDto->getPhpCodebaseSources());
        $this->assertNotNull($projectKey);
        $this->assertInstanceOf('\Codebase\Application\Dto\CodebaseInterface', $projectKey);
    }

    /**
     * @return void
     */
    public function testParseSourceWithInvalidPath(): void
    {
        //Assert
        $this->expectException(DirectoryNotFoundException::class);

        //Arrange
        $codebaseRequestDto = new SourceParserRequestDto(
            [APPLICATION_ROOT_DIR . '/invalidPath/'],
            [APPLICATION_ROOT_DIR . '/invalidPath/'],
            ['TestCore'],
            [],
        );

        //Act
        static::bootKernel()->getContainer()->get(SourceParser::class)->parseSource($codebaseRequestDto);
    }
}
