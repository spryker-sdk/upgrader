<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\Git;

use DateTime;
use ReflectionClass;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\IntegratorResponseDto;
use Upgrade\Application\Dto\ReleaseGroupFilterResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup\MajorVersionValidator;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerLockComparatorCommandExecutor;
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
        $this->assertSame('upgradebot/upgrade-bot', $stepsExecutionDto->getTargetBranch());
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
    public function testCreatePullRequestUnSuccessCaseNoRequiredProperty(): void
    {
        $stepsExecutionDto = new StepsResponseDto(true);
        $composerLockDiffDto = new ComposerLockDiffDto();
        $stepsExecutionDto->setComposerLockDiff($composerLockDiffDto);

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
        $stepsExecutionDto = new StepsResponseDto(true);

        $stepsExecutionDto->setComposerLockDiff($this->getComposerLockDiffDto());
        $stepsExecutionDto->addBlocker(new ValidatorViolationDto(MajorVersionValidator::getValidatorTitle(), 'Available major info'));
        $integratorResponseDto = new IntegratorResponseDto([
            'message-list' => ['Test message'],
            'warning-list' => ['Manifest for Spryker.AuthenticationOauth:1.0.0 was skipped. Please, update it to use full functionality.'],
        ]);
        $stepsExecutionDto->addIntegratorResponseDto($integratorResponseDto);

        $processRunnerMock = $this->mockProcessRunnerWithOutput('');
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        $gitHubProviderMock = $this->getMockBuilder(GitHubSourceCodeProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stepsExecutionDto->addFilterResponse(
            new ReleaseGroupFilterResponseDto(
                $this->getReleaseGroupDto(),
                new ModuleDtoCollection([
                    new ModuleDto('spryker/shipment-types-backend-api', '0.1.0', 'minor'),
                ]),
            ),
        );

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
    public function testHasUncommittedFile(): void
    {
        // Arrange
        $filename = 'integrator.lock';
        $processRunnerMock = $this->mockProcessRunnerWithOutput("M $filename");
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        // Act
        $isExist = $git->hasUncommittedFile($filename);

        // Assert
        $this->assertTrue($isExist);
    }

    /**
     * @return void
     */
    public function testHasUncommittedFileFails(): void
    {
        // Arrange
        $filename = 'integrator.lock';
        $processRunnerMock = $this->mockProcessRunnerWithOutput('');
        $git = $this->getGitWithProcessRunner($processRunnerMock);

        // Act
        $isExist = $git->hasUncommittedFile($filename);

        // Assert
        $this->assertFalse($isExist);
    }

    /**
     * @param \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerService $processRunner
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
     * @return \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerService
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
        $configurationProviderMock->method('getBranchPattern')->willReturn('upgradebot/upgrade-bot');

        return $configurationProviderMock;
    }

    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto
     */
    protected function getComposerLockDiffDto(): ComposerLockDiffDto
    {
        $message = '{"changes":{"spryker\/product-label":["3.2.0","3.3.0","https:\/\/github.com\/spryker\/product-label\/compare\/3.2.0...3.3.0"]},"changes-dev":{"spryker-shop\/web-profiler-widget":["1.4.1","1.4.2","https:\/\/github.com\/spryker-shop\/web-profiler-widget\/compare\/1.4.1...1.4.2"]}}';
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn($message);

        $processRunnerMockMock = $this->createMock(ProcessRunnerService::class);
        $processRunnerMockMock->method('run')->willReturn($processMock);

        $composerLockComparatorCommandExecutor = new ComposerLockComparatorCommandExecutor($processRunnerMockMock);

        return $composerLockComparatorCommandExecutor->getComposerLockDiff();
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    protected function getReleaseGroupDto(): ReleaseGroupDto
    {
        $releaseGroupDto = new ReleaseGroupDto(
            4821,
            'RGname',
            new ModuleDtoCollection(),
            new DateTime(),
            true,
            'https://api.release.spryker.com/release-group/4821',
            100,
            false,
        );
        $releaseGroupDto->setJiraIssue('CC-25420');
        $releaseGroupDto->setJiraIssueLink('https://spryker.atlassian.net/browse/CC-25420');

        return $releaseGroupDto;
    }
}
