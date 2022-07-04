<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodebaseTest\Infrastructure\SourceParser\Parser;

use Codebase\Infrastructure\SourceParser\Parser\PhpParser;

class PhpParserTest extends BaseParser
{
    /**
     * @var string
     */
    protected const PHP_EXTENSION = 'php';

    /**
     * @return void
     */
    public function testParse(): void
    {
        //Arrange
        $codebaseSourceDto = $this->createCodebaseSourceDto();

        //Act
        $codebaseSourceDto = $this->runParser(PhpParser::class, $codebaseSourceDto, static::PHP_EXTENSION);

        //Assert
        $codebasePhpSources = $codebaseSourceDto->getPhpCodebaseSources();
        $this->assertNotEmpty($codebasePhpSources);
        $this->assertNotNull($codebasePhpSources['TestProject\TestClassProjectConstant'] ?? null);
        $this->assertNull($codebasePhpSources['TestCore\TestClassCoreConstant'] ?? null);

        foreach ($codebaseSourceDto->getPhpCodebaseSources() as $phpCodebaseSource) {
            $this->assertInstanceOf('\Codebase\Application\Dto\ClassCodebaseDto', $phpCodebaseSource);
        }
    }
}
