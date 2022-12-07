<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\Git;

use Core\Infrastructure\Service\ProcessRunnerService;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Git\Git;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub\GitHubSourceCodeProvider;

class GitTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testIsRemoteTargetBranchNotExistSuccessCase(): void
    {
        // Arrange
        $processRunnerMock = $this->mockProcessRunnerWithOutput('');
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        // Act
        $stepsExecutionDto = $git->isRemoteTargetBranchNotExist((new StepsResponseDto(true)));

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testIsRemoteTargetBranchNotExistUnSuccessCase(): void
    {
        // Arrange
        $processRunnerMock = $this->mockProcessRunnerWithOutput('d50f9e5f2062ff54c4c192fecd853c5983b3a600');
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        // Act
        $stepsExecutionDto = $git->isRemoteTargetBranchNotExist((new StepsResponseDto(true)));

        // Assert
        $this->assertFalse($stepsExecutionDto->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testIsLocalTargetBranchNotExistSuccessCase(): void
    {
        // Arrange
        $processRunnerMock = $this->mockProcessRunnerWithOutput('', false);
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        // Act
        $stepsExecutionDto = $git->isLocalTargetBranchNotExist((new StepsResponseDto(true)));

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testIsLocalTargetBranchNotExistUnSuccessCase(): void
    {
        // Arrange
        $processRunnerMock = $this->mockProcessRunnerWithOutput('d50f9e5f2062ff54c4c192fecd853c5983b3a600');
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        // Act
        $stepsExecutionDto = $git->isLocalTargetBranchNotExist((new StepsResponseDto(true)));

        // Assert
        $this->assertFalse($stepsExecutionDto->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testHasAnyUncommittedChangesSuccessCase(): void
    {
        // Arrange
        $processRunnerMock = $this->mockProcessRunnerWithOutput('');
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        // Act
        $stepsExecutionDto = $git->hasAnyUncommittedChanges((new StepsResponseDto(true)));

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testHasAnyUncommittedChangesUnSuccessCase(): void
    {
        // Arrange
        $processRunnerMock = $this->mockProcessRunnerWithOutput('d50f9e5f2062ff54c4c192fecd853c5983b3a600');
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        // Act
        $stepsExecutionDto = $git->hasAnyUncommittedChanges((new StepsResponseDto(true)));

        // Assert
        $this->assertFalse($stepsExecutionDto->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testFindChangedFiles(): void
    {
        // Arrange
        $processRunnerMock = $this->mockProcessRunnerWithOutput(
            'src/Pyz/Zed/ProductAlternative/ProductAlternativeDependencyProvider.php' . PHP_EOL
            . 'src/Pyz/Zed/ProductAlternativeStorage/ProductAlternativeStorageConfig.php' . PHP_EOL
        );
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        // Act
        $stepsExecutionDto = $git->findChangedFiles((new StepsResponseDto(true)));

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
        $this->assertEquals([
            'src/Pyz/Zed/ProductAlternative/ProductAlternativeDependencyProvider.php',
            'src/Pyz/Zed/ProductAlternativeStorage/ProductAlternativeStorageConfig.php',
        ], $stepsExecutionDto->getChangedFiles());
    }

    /**
     * @return void
     */
    public function testCreatePullRequestUnSuccessCaseNoRequiredProperty(): void
    {
        $stepsExecutionDto = new StepsResponseDto(true);
        $composerLockDiffDto = new ComposerLockDiffDto([]);
        $stepsExecutionDto->addComposerLockDiff($composerLockDiffDto);

        $processRunnerMock = $this->mockProcessRunnerWithOutput('');
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        $gitHubProviderMock = $this->getMockBuilder(GitHubSourceCodeProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Assert
        $gitHubProviderMock->expects($this->exactly(1))
            ->method('createPullRequest')
            ->willReturn($stepsExecutionDto);

        // Arrange
        $reflection = new ReflectionClass($git);
        $reflectionProperty = $reflection->getProperty('sourceCodeProvider');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($git, $gitHubProviderMock);

        // Act
        $stepsExecutionDto = $git->createPullRequest($stepsExecutionDto);

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
    }

    /**
     * @return void
     */
    public function testCreatePullRequestSuccessCase(): void
    {
        // Arrange
        $message = '{"changes":{"spryker\/product-label":["3.2.0","3.3.0","https:\/\/github.com\/spryker\/product-label\/compare\/3.2.0...3.3.0"]},"changes-dev":{"spryker-shop\/web-profiler-widget":["1.4.1","1.4.2","https:\/\/github.com\/spryker-shop\/web-profiler-widget\/compare\/1.4.1...1.4.2"]}}';
        $stepsExecutionDto = new StepsResponseDto(true);
        $composerLockDiffDto = new ComposerLockDiffDto(json_decode($message, true));
        $stepsExecutionDto->addComposerLockDiff($composerLockDiffDto);

        $processRunnerMock = $this->mockProcessRunnerWithOutput('');
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        $gitHubProviderMock = $this->getMockBuilder(GitHubSourceCodeProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Assert
        $gitHubProviderMock->expects($this->exactly(1))
            ->method('createPullRequest')
            ->willReturn($stepsExecutionDto);

        // Arrange
        $reflection = new ReflectionClass($git);
        $reflectionProperty = $reflection->getProperty('sourceCodeProvider');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($git, $gitHubProviderMock);

        // Act
        $stepsExecutionDto = $git->createPullRequest($stepsExecutionDto);

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
    }

    /**
     * @param \Core\Infrastructure\Service\ProcessRunnerService $processRunner
     *
     * @return \Upgrade\Infrastructure\VersionControlSystem\Git\Git
     */
    protected function getGitWithProcessRunner(ProcessRunnerService $processRunner): Git
    {
        /** @var \Upgrade\Infrastructure\VersionControlSystem\Git\Git $git */
        $git = static::bootKernel()->getContainer()->get(Git::class);

        $gitReflection = new ReflectionClass($git);
        $gitProperty = $gitReflection->getProperty('processRunner');
        $gitProperty->setAccessible(true);
        $gitProperty->setValue($git, $processRunner);

        $gitProperty = $gitReflection->getProperty('configurationProvider');
        $gitProperty->setAccessible(true);
        $gitProperty->setValue($git, $this->mockConfigurationProvider());

        return $git;
    }

    /**
     * @param string $outputMessage
     * @param bool $isSuccessful
     *
     * @return \Core\Infrastructure\Service\ProcessRunnerService
     */
    protected function mockProcessRunnerWithOutput(string $outputMessage, bool $isSuccessful = true): ProcessRunnerService
    {
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn($outputMessage);
        $processMock->method('isSuccessful')->willReturn($isSuccessful);

        $processRunnerMock = $this->createMock(ProcessRunnerService::class);
        $processRunnerMock->method('run')->willReturn($processMock);

        return $processRunnerMock;
    }

    /**
     * @return \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected function mockConfigurationProvider(): ConfigurationProvider
    {
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $configurationProviderMock->method('getAccessToken')->willReturn('-');
        $configurationProviderMock->method('getRepositoryName')->willReturn('-');
        $configurationProviderMock->method('getOrganizationName')->willReturn('-');

        return $configurationProviderMock;
    }
}
