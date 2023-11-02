<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab;

use Gitlab\Client as GitLabClient;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab\GitLabClientFactory;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab\GitLabSourceCodeProvider;

class GitLabSourceCodeProviderTest extends TestCase
{
    /**
     * @dataProvider validateCredentialsDataProvider
     *
     * @param string $hasAccessToken
     * @param string $hasProjectId
     * @param string $hasOrganizationName
     * @param string $hasRepositoryName
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testValidateCredentialsShouldValidateCorrectly(
        string $hasAccessToken,
        string $hasProjectId,
        string $hasOrganizationName,
        string $hasRepositoryName,
        bool $expectedResult
    ): void {
        // Arrange
        $configurationMock = $this->createMock(ConfigurationProvider::class);
        $configurationMock->method('getAccessToken')->willReturn($hasAccessToken);
        $configurationMock->method('getProjectId')->willReturn($hasProjectId);
        $configurationMock->method('getOrganizationName')->willReturn($hasOrganizationName);
        $configurationMock->method('getRepositoryName')->willReturn($hasRepositoryName);

        $gitLabSourceCodeProvider = new GitLabSourceCodeProvider($configurationMock, $this->createGitLabClientFactoryMock());
        $stepsResponseDto = new StepsResponseDto(true);

        // Act
        $gitLabSourceCodeProvider->validateCredentials($stepsResponseDto);

        // Assert
        $this->assertEquals($expectedResult, $stepsResponseDto->getIsSuccessful());
    }

    /**
     * @return array<array<mixed>>
     */
    public function validateCredentialsDataProvider(): array
    {
        return [
            ['access_token', 'project_id', 'organization_name', 'repository_name', true],
            ['', 'project_id', 'organization_name', 'repository_name', false],
            ['access_token', '', '', '', false],
            ['access_token', 'project_id', '', '', true],
            ['access_token', '', 'organization_name', '', false],
            ['access_token', '', '', 'repository_name', false],
            ['access_token', '', 'organization_name', 'repository_name', true],
        ];
    }

    /**
     * @return \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab\GitLabClientFactory
     */
    protected function createGitLabClientFactoryMock(): GitLabClientFactory
    {
        $gitLabClientFactory = $this->createMock(GitLabClientFactory::class);
        $gitLabClientFactory->method('getClient')->willReturn($this->createMock(GitLabClient::class));

        return $gitLabClientFactory;
    }

    /**
     * @return array<array<mixed>>
     */
    public function createPullRequestDataProvider(): array
    {
        return [
            // Invalid credentials
            ['', '', '', '', 'Please check defined values of environment variables: ACCESS_TOKEN and (PROJECT_ID or (ORGANIZATION_NAME and REPOSITORY_NAME)).'],
            ['access_token', '', '', '', 'Please check defined values of environment variables: ACCESS_TOKEN and (PROJECT_ID or (ORGANIZATION_NAME and REPOSITORY_NAME)).'],
            ['access_token', 'project_id', '', '', ''],
            ['access_token', '', 'org_name', 'repo_name', ''],
            // Add more test cases as needed
        ];
    }

    /**
     * @dataProvider createPullRequestDataProvider
     *
     * @param string $accessToken
     * @param string $projectId
     * @param string $orgName
     * @param string $repoName
     * @param string $expectedError
     *
     * @return void
     */
    public function testCreatePullRequest(string $accessToken, string $projectId, string $orgName, string $repoName, string $expectedError): void
    {
        // Set up mocks and dependencies

        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $gitLabClientFactoryMock = $this->createMock(GitLabClientFactory::class);

        $gitLabSourceCodeProvider = new GitLabSourceCodeProvider(
            $configurationProviderMock,
            $gitLabClientFactoryMock,
        );

        // Configure the mocks
        $configurationProviderMock->method('getAccessToken')->willReturn($accessToken);
        $configurationProviderMock->method('getProjectId')->willReturn($projectId);
        $configurationProviderMock->method('getOrganizationName')->willReturn($orgName);
        $configurationProviderMock->method('getRepositoryName')->willReturn($repoName);

        // Create mock DTOs
        $stepsExecutionDto = new StepsResponseDto();
        $pullRequestDto = new PullRequestDto('', '', '');

        // Create a mock for the GitLab client and its methods used in the createPullRequest method
        $gitLabClientMock = $this->getMockBuilder(GitLabClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gitLabClientFactoryMock->method('getClient')->willReturn($gitLabClientMock);

        if ($expectedError) {
            $stepsExecutionDto = $gitLabSourceCodeProvider->createPullRequest($stepsExecutionDto, $pullRequestDto);
            $this->assertFalse($stepsExecutionDto->getIsSuccessful());
            $this->assertInstanceOf(Error::class, $stepsExecutionDto->getError());
            $this->assertEquals($expectedError, $stepsExecutionDto->getError()->getErrorMessage());
        } else {
            $this->assertTrue($stepsExecutionDto->getIsSuccessful());
            $this->assertNull($stepsExecutionDto->getError());
        }
    }

    /**
     * @dataProvider buildBlockerTextBlockDataProvider
     *
     * @return void
     */
    public function testBuildBlockerTextBlock($title, $message, $expectedOutput): void
    {
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $gitLabClientFactoryMock = $this->createMock(GitLabClientFactory::class);

        $gitLabSourceCodeProvider = new GitLabSourceCodeProvider(
            $configurationProviderMock,
            $gitLabClientFactoryMock,
        );

        $result = $gitLabSourceCodeProvider->buildBlockerTextBlock(new ValidatorViolationDto($title, $message));

        $this->assertSame($expectedOutput, $result);
    }

    /**
     * @return array<array>
     */
    public function buildBlockerTextBlockDataProvider(): array
    {
        return [
            ['Title 1', 'Message 1', "> <b>Title 1.</b> Message 1\n <br>\n"],
            ['Another Title', 'Another Message', "> <b>Another Title.</b> Another Message\n <br>\n"],
        ];
    }
}
