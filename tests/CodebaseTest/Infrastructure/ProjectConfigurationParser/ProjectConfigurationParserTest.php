<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodebaseTest\Infrastructure\ProjectConfigurationParser;

use Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException;
use Codebase\Infrastructure\ToolingConfigurationReader\Reader\IgnoredRulesReader;
use Codebase\Infrastructure\ToolingConfigurationReader\Reader\ProjectPrefixesReader;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReader;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProjectConfigurationParserTest extends KernelTestCase
{
 /**
  * @return void
  */
    public function testEmptyFile(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new IgnoredRulesReader(), new ProjectPrefixesReader()]);

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-empty.yml');

        //Assert
        $this->assertSame(['Pyz'], $configurationResponseDto->getProjectPrefixes());
        $this->assertSame([], $configurationResponseDto->getIgnoredRules());
    }

    /**
     * @return void
     */
    public function testPrefixesSuccessParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesReader()]);

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-prefixes.yml');

        //Assert
        $this->assertSame(['Pyz', 'Zyp'], $configurationResponseDto->getProjectPrefixes());
    }

    /**
     * @return void
     */
    public function testPrefixesParseDefaultValueForNotExistFile(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesReader()]);

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('not-exists-tooling.yml');

        //Assert
        $this->assertSame(['Pyz'], $configurationResponseDto->getProjectPrefixes());
    }

    /**
     * @return void
     */
    public function testPrefixesParseDefaultValueForNotExistKey(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesReader()]);

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-prefixes-invalid-key.yml');

        //Assert
        $this->assertSame(['Pyz'], $configurationResponseDto->getProjectPrefixes());
    }

    /**
     * @return void
     */
    public function testPrefixesInvalidTypeOneParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesReader()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Value of evaluator.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-prefixes-invalid-value-1.yml');
    }

    /**
     * @return void
     */
    public function testPrefixesInvalidTypeTwoParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesReader()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Value of evaluator.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-prefixes-invalid-value-2.yml');
    }

    /**
     * @return void
     */
    public function testPrefixesInvalidTypeThreeParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new ProjectPrefixesReader()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Value of evaluator.prefixes should be array of string');

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-prefixes-invalid-value-3.yml');
    }

    /**
     * @return void
     */
    public function testRulesIgnoreSuccessParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new IgnoredRulesReader()]);

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('tests/data/Evaluate/Project/tooling-rules-ignore.yml');

        //Assert
        $this->assertSame(['Rule1', 'Rule2'], $configurationResponseDto->getIgnoredRules());
    }

    /**
     * @return void
     */
    public function testRulesIgnoreParseDefaultValueForNotExistFile(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new IgnoredRulesReader()]);

        //Act
        $configurationResponseDto = $parser->readToolingConfiguration('not-exists-tooling.yml');

        //Assert
        $this->assertSame([], $configurationResponseDto->getIgnoredRules());
    }

    /**
     * @return void
     */
    public function testIgnoredRulesReaderInvalidTypeOneParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new IgnoredRulesReader()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Value of evaluator.rules.ignore should be array of string');

        //Act
        $configurationFilePath = 'tests/data/Evaluate/Project/tooling-rules-ignore-invalid-value-1.yml';
        $configurationResponseDto = $parser->readToolingConfiguration($configurationFilePath);
    }

    /**
     * @return void
     */
    public function testRulesIgnoreInvalidTypeTwoParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new IgnoredRulesReader()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Value of evaluator.rules.ignore should be array of string');

        //Act
        $configurationFilePath = 'tests/data/Evaluate/Project/tooling-rules-ignore-invalid-value-2.yml';
        $configurationResponseDto = $parser->readToolingConfiguration($configurationFilePath);
    }

    /**
     * @return void
     */
    public function testRulesIgnoreInvalidTypeThreeParse(): void
    {
        //Arrange
        $parser = new ToolingConfigurationReader([new IgnoredRulesReader()]);

        //Assert
        $this->expectException(ProjectConfigurationFileInvalidSyntaxException::class);
        $this->expectExceptionMessage('Invalid tooling configuration file structure. Value of evaluator.rules.ignore should be array of string');

        //Act
        $configurationFilePath = 'tests/data/Evaluate/Project/tooling-rules-ignore-invalid-value-3.yml';
        $configurationResponseDto = $parser->readToolingConfiguration($configurationFilePath);
    }
}
