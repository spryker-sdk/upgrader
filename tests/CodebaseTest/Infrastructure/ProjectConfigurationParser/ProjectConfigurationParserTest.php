<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodebaseTest\Infrastructure\ProjectConfigurationParser;

use Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProjectConfigurationParserTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testSuccessParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader();

        //Act
        $configurationResponseDto = $parser->readConfiguration('tests/data/Evaluate/Project/tooling.yml');

        //Assert
        $this->assertEquals(['Pyz', 'Zyp'], $configurationResponseDto->getProjectPrefixes());
    }

    /**
     * @return void
     */
    public function testParseDefaultValue(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader();

        //Act
        $configurationResponseDto = $parser->readConfiguration('not-exists-tooling.yml');

        //Assert
        $this->assertEquals(['Pyz'], $configurationResponseDto->getProjectPrefixes());
    }

    /**
     * @return void
     */
    public function testInvalidKeyParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader();

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid configuration file tests/data/Evaluate/Project/tooling-invalid-key.yml. Undefined array key "upgrader"');

        //Act
        $configurationResponseDto = $parser->readConfiguration('tests/data/Evaluate/Project/tooling-invalid-key.yml');
    }

    /**
     * @return void
     */
    public function testInvalidTypeOneParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader();

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid configuration file tests/data/Evaluate/Project/tooling-invalid-value-1.yml. Value of upgrader.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->readConfiguration('tests/data/Evaluate/Project/tooling-invalid-value-1.yml');
    }

    /**
     * @return void
     */
    public function testInvalidTypeTwoParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader();

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid configuration file tests/data/Evaluate/Project/tooling-invalid-value-2.yml. Value of upgrader.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->readConfiguration('tests/data/Evaluate/Project/tooling-invalid-value-2.yml');
    }

    /**
     * @return void
     */
    public function testInvalidTypeThreeParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader();

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid configuration file tests/data/Evaluate/Project/tooling-invalid-value-3.yml. Value of upgrader.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->readConfiguration('tests/data/Evaluate/Project/tooling-invalid-value-3.yml');
    }
}
