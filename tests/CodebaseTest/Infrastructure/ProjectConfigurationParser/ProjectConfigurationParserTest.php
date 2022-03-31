<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodebaseTest\Infrastructure\ProjectConfigurationParser;

use Codebase\Application\Dto\ConfigurationRequestDto;
use Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException;
use Codebase\Infrastructure\ProjectConfigurationParser\ProjectConfigurationParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProjectConfigurationParserTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testSuccessParse(): void
    {
        //Arrange
        $requestDto = new ConfigurationRequestDto(
            'tests/data/Evaluate/Project/tooling.yml',
            'src/',
        );
        $parser = new ProjectConfigurationParser();

        //Act
        $configurationResponseDto = $parser->parseConfiguration($requestDto);

        //Assert
        $this->assertEquals(['Pyz', 'Zyp'], $configurationResponseDto->getProjectPrefixes());
        $this->assertEquals(['src/Pyz/', 'src/Zyp/'], $configurationResponseDto->getProjectDirectories());
    }

    /**
     * @return void
     */
    public function testParseDefaultValue(): void
    {
        //Arrange
        $requestDto = new ConfigurationRequestDto(
            'not-exists-tooling.yml',
            'src/',
        );
        $parser = new ProjectConfigurationParser();

        //Act
        $configurationResponseDto = $parser->parseConfiguration($requestDto);

        //Assert
        $this->assertEquals(['Pyz'], $configurationResponseDto->getProjectPrefixes());
        $this->assertEquals(['src/Pyz/'], $configurationResponseDto->getProjectDirectories());
    }

    /**
     * @return void
     */
    public function testInvalidKeyParse(): void
    {
        //Arrange
        $requestDto = new ConfigurationRequestDto(
            'tests/data/Evaluate/Project/tooling-invalid-key.yml',
            'src/',
        );
        $parser = new ProjectConfigurationParser();

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid configuration file tests/data/Evaluate/Project/tooling-invalid-key.yml. Undefined array key "upgrader"');

        //Act
        $configurationResponseDto = $parser->parseConfiguration($requestDto);
    }

    /**
     * @return void
     */
    public function testInvalidTypeOneParse(): void
    {
        //Arrange
        $requestDto = new ConfigurationRequestDto(
            'tests/data/Evaluate/Project/tooling-invalid-value-1.yml',
            'src/',
        );
        $parser = new ProjectConfigurationParser();

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid configuration file tests/data/Evaluate/Project/tooling-invalid-value-1.yml. Value of upgrader.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->parseConfiguration($requestDto);
    }

    /**
     * @return void
     */
    public function testInvalidTypeTwoParse(): void
    {
        //Arrange
        $requestDto = new ConfigurationRequestDto(
            'tests/data/Evaluate/Project/tooling-invalid-value-2.yml',
            'src/',
        );
        $parser = new ProjectConfigurationParser();

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid configuration file tests/data/Evaluate/Project/tooling-invalid-value-2.yml. Value of upgrader.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->parseConfiguration($requestDto);
    }

    /**
     * @return void
     */
    public function testInvalidTypeThreeParse(): void
    {
        //Arrange
        $requestDto = new ConfigurationRequestDto(
            'tests/data/Evaluate/Project/tooling-invalid-value-3.yml',
            'src/',
        );
        $parser = new ProjectConfigurationParser();

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid configuration file tests/data/Evaluate/Project/tooling-invalid-value-3.yml. Value of upgrader.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->parseConfiguration($requestDto);
    }
}
