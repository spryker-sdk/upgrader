<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodebaseTest\Infrastructure\ProjectConfigurationParser;

use Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReader;
use Codebase\Infrastructure\ToolingConfigurationReader\Validator\ProjectPrefixesValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProjectConfigurationParserTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testSuccessParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesValidator()]);

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling.yml');

        //Assert
        $this->assertSame(['Pyz', 'Zyp'], $configurationResponseDto->getProjectPrefixes());
    }

    /**
     * @return void
     */
    public function testParseDefaultValue(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesValidator()]);

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('not-exists-tooling.yml');

        //Assert
        $this->assertSame(['Pyz'], $configurationResponseDto->getProjectPrefixes());
    }

    /**
     * @return void
     */
    public function testInvalidKeyParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesValidator()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Key upgrader.prefixes not exist');

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-invalid-key.yml');
    }

    /**
     * @return void
     */
    public function testInvalidTypeOneParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesValidator()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Value of upgrader.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-invalid-value-1.yml');
    }

    /**
     * @return void
     */
    public function testInvalidTypeTwoParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesValidator()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Value of upgrader.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-invalid-value-2.yml');
    }

    /**
     * @return void
     */
    public function testInvalidTypeThreeParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesValidator()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Value of upgrader.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-invalid-value-3.yml');
    }
}
