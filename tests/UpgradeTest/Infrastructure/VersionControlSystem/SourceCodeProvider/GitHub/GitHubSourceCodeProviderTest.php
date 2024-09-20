<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub;

use Github\Api\PullRequest;
use Github\Api\PullRequest\ReviewRequest;
use GitHub\Client as GitHubClient;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\Generator\OutputMessageBuilder;
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
            ['', '', '', [], 'Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.', false],
            ['access_token', '', '', [], 'Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.', false],
            ['access_token', 'org_name', '', [], 'Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.', false],
            ['access_token', 'org_name', 'repo_name', [], 'some error', true],
            ['access_token', 'org_name', 'repo_name', ['github_user', 'github_user2'], '', false],
        ];
    }

    /**
     * @dataProvider createPullRequestDataProvider
     *
     * @param string $accessToken
     * @param string $orgName
     * @param string $repoName
     * @param array $reviewers
     * @param string $expectedError
     * @param bool $produceException
     *
     * @return void
     */
    public function testCreatePullRequest(
        string $accessToken,
        string $orgName,
        string $repoName,
        array $reviewers,
        string $expectedError,
        bool $produceException
    ): void {
        $prMock = $this->createMock(PullRequest::class);
        $prMock->method('create')->willReturn(['html_url' => 'some_url', 'number' => 123]);

        if ($reviewers) {
            $reviewMock = $this->createMock(ReviewRequest::class);
            $reviewMock->expects($this->once())
                ->method('create')
                ->with($orgName, $repoName, 123, $reviewers);
            $prMock->method('reviewRequests')->willReturn($reviewMock);
        }

        // Create a mock for the GitHub client and its methods used in the createPullRequest method
        $gitHubClientMock = $this->createGitHubClientMock($produceException ? null : $prMock);

        // Set up mocks and dependencies
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $gitHubClientFactoryMock = $this->createMock(GitHubClientFactory::class);

        $gitHubSourceCodeProvider = new GitHubSourceCodeProvider(
            $configurationProviderMock,
            $gitHubClientFactoryMock,
            new OutputMessageBuilder(),
        );
        $gitHubClientFactoryMock->method('getClient')->willReturn($gitHubClientMock);

        // Configure the mocks
        $configurationProviderMock->method('getAccessToken')->willReturn($accessToken);
        $configurationProviderMock->method('getOrganizationName')->willReturn($orgName);
        $configurationProviderMock->method('getRepositoryName')->willReturn($repoName);
        $configurationProviderMock->method('getPullRequestReviewers')->willReturn($reviewers);

        // Create mock DTOs
        $stepsExecutionDto = new StepsResponseDto();
        $pullRequestDto = new PullRequestDto('', '', '');

        $stepsExecutionDto = $gitHubSourceCodeProvider->createPullRequest($stepsExecutionDto, $pullRequestDto);

        if ($expectedError) {
            $this->assertFalse($stepsExecutionDto->getIsSuccessful());
            $this->assertInstanceOf(Error::class, $stepsExecutionDto->getError());
            $this->assertEquals($expectedError, $stepsExecutionDto->getError()->getErrorMessage());
        } else {
            $this->assertTrue($stepsExecutionDto->getIsSuccessful());
            $this->assertNull($stepsExecutionDto->getError());
            $this->assertStringContainsString('Link to pull request some_url', $stepsExecutionDto->getOutputMessage());
        }
    }

    /**
     * @param \Github\Api\PullRequest|null $pr
     *
     * @return \GitHub\Client
     */
    protected function createGitHubClientMock(?PullRequest $pr): GitHubClient
    {
        // Create a mock for the GitHub client and its methods used in the createPullRequest method
        $gitHubClientMock = $this->getMockBuilder(GitHubClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['pr'])
            ->getMock();

        if (!$pr) {
            $gitHubClientMock->method('pr')->willThrowException(new RuntimeException('some error'));

            return $gitHubClientMock;
        }

        $gitHubClientMock->method('pr')->willReturn($pr);

        return $gitHubClientMock;
    }

    /**
     * @dataProvider buildBlockerTextBlockDataProvider
     *
     * @param string $title
     * @param string $message
     * @param string $expectedOutput
     *
     * @return void
     */
    public function testBuildBlockerTextBlock(string $title, string $message, string $expectedOutput): void
    {
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $gitHubClientFactoryMock = $this->createMock(GitHubClientFactory::class);

        $gitHubSourceCodeProvider = new GitHubSourceCodeProvider(
            $configurationProviderMock,
            $gitHubClientFactoryMock,
            new OutputMessageBuilder(),
        );

        $result = $gitHubSourceCodeProvider->buildBlockerTextBlock(new ValidatorViolationDto($title, $message));

        $this->assertSame($expectedOutput, $result);
    }

    /**
     * @dataProvider testBuildBlockerTextBlockTruncatesErrorTraceDataProvider
     *
     * @param string $title
     * @param string $message
     * @param string $expectedOutput
     *
     * @return void
     */
    public function testBuildBlockerTextBlockTruncatesErrorTrace(string $title, string $message, string $expectedOutput): void
    {
        $configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $configurationProviderMock
            ->method('isTruncateErrorTracesInPrsEnabled')
            ->willReturn(true);
        $gitHubClientFactoryMock = $this->createMock(GitHubClientFactory::class);

        $gitHubSourceCodeProvider = new GitHubSourceCodeProvider(
            $configurationProviderMock,
            $gitHubClientFactoryMock,
            new OutputMessageBuilder(),
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

    /**
     * @return array<array>
     */
    public function testBuildBlockerTextBlockTruncatesErrorTraceDataProvider(): array
    {
        return [
            [
                'Title 1',
                '<b>Test error message.</b>[stacktrace] #0 /first/row/of/trace/#1 /second/row/of/trace/ #3 /third/row/of/trace/',
                "> [!IMPORTANT] \n> <b>Title 1.</b> <b>Test error message.</b> #0 /first/row/of/trace/[...trace truncated...]\n",
            ],
            [
                'Title 2',
                '<u>Another test error message.</u>[stacktrace] #0 /first/row/of/trace/#1 /second/row/of/trace/ #3 /third/row/of/trace/',
                "> [!IMPORTANT] \n> <b>Title 2.</b> <u>Another test error message.</u> #0 /first/row/of/trace/[...trace truncated...]\n",
            ],
        ];
    }
}
