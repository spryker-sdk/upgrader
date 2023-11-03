<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub;

use GitHub\Client as GitHubClient;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub\GitHubClientFactory;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub\GitHubSourceCodeProvider;

class GitHubSourceCodeProviderTest extends TestCase
{
    /**
     * @return array<array<mixed>>
     */
    public function createPullRequestDataProvider(): array
    {
        return [
            // Invalid credentials
            ['', '', '', 'Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.'],
            ['access_token', '', '', 'Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.'],
            ['access_token', 'org_name', '', 'Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.'],
            ['access_token', 'org_name', 'repo_name', ''],
        ];
    }

    /**
     * @dataProvider createPullRequestDataProvider
     *
     * @param string $accessToken
     * @param string $orgName
     * @param string $repoName
     * @param string $expectedError
     *
     * @return void
     */
    public function testCreatePullRequest(string $accessToken, string $orgName, string $repoName, string $expectedError): void
    {
        // Set up mocks and dependencies
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $gitHubClientFactoryMock = $this->createMock(GitHubClientFactory::class);

        $gitHubSourceCodeProvider = new GitHubSourceCodeProvider(
            $configurationProviderMock,
            $gitHubClientFactoryMock,
        );

        // Configure the mocks
        $configurationProviderMock->method('getAccessToken')->willReturn($accessToken);
        $configurationProviderMock->method('getOrganizationName')->willReturn($orgName);
        $configurationProviderMock->method('getRepositoryName')->willReturn($repoName);

        // Create mock DTOs
        $stepsExecutionDto = new StepsResponseDto();
        $pullRequestDto = new PullRequestDto('', '', '');

        // Create a mock for the GitHub client and its methods used in the createPullRequest method
        $gitHubClientMock = $this->getMockBuilder(GitHubClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $gitHubClientFactoryMock->method('getClient')->willReturn($gitHubClientMock);

        if ($expectedError) {
            $stepsExecutionDto = $gitHubSourceCodeProvider->createPullRequest($stepsExecutionDto, $pullRequestDto);
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
     * @param $title
     * @param $message
     * @param $expectedOutput
     *
     * @return void
     */
    public function testBuildBlockerTextBlock($title, $message, $expectedOutput): void
    {
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $gitHubClientFactoryMock = $this->createMock(GitHubClientFactory::class);

        $gitHubSourceCodeProvider = new GitHubSourceCodeProvider(
            $configurationProviderMock,
            $gitHubClientFactoryMock,
        );

        $result = $gitHubSourceCodeProvider->buildBlockerTextBlock(new ValidatorViolationDto($title, $message));

        $this->assertSame($expectedOutput, $result);
    }

    /**
     * @return array<array>
     */
    public function buildBlockerTextBlockDataProvider(): array
    {
        return [
            ['Title 1', 'Message 1', "> [!IMPORTANT] \n> <b>Title 1.</b> Message 1\n"],
            ['Another Title', 'Another Message', "> [!IMPORTANT] \n> <b>Another Title.</b> Another Message\n"],
        ];
    }
}
