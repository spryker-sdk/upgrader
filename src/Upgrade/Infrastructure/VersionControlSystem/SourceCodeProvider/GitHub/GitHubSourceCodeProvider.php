<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub;

use RuntimeException;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\Generator\OutputMessageBuilder;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class GitHubSourceCodeProvider implements SourceCodeProviderInterface
{
    /**
     * @var string
     */
    protected const HTML_URL_KEY = 'html_url';

    /**
     * @var string
     */
    protected const NUMBER_KEY = 'number';

    /**
     * @var string
     */
    protected const STRING_STACK_TRACE = '[stacktrace]';

    /**
     * @var string
     */
    protected const STRING_NUMBER_ONE = '#1';

    /**
     * @var string
     */
    protected const STRING_TRACE_TRUNCATED = '[...trace truncated...]';

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub\GitHubClientFactory
     */
    protected GitHubClientFactory $gitHubClientFactory;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Generator\OutputMessageBuilder
     */
    protected OutputMessageBuilder $outputMessageBuilder;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub\GitHubClientFactory $gitHubClientFactory
     * @param \Upgrade\Infrastructure\VersionControlSystem\Generator\OutputMessageBuilder $outputMessageBuilder
     */
    public function __construct(
        ConfigurationProvider $configurationProvider,
        GitHubClientFactory $gitHubClientFactory,
        OutputMessageBuilder $outputMessageBuilder
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->gitHubClientFactory = $gitHubClientFactory;
        $this->outputMessageBuilder = $outputMessageBuilder;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return ConfigurationProvider::GITHUB_SOURCE_CODE_PROVIDER;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function validateCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if (
            !$this->configurationProvider->getAccessToken() ||
            !$this->configurationProvider->getOrganizationName() ||
            !$this->configurationProvider->getRepositoryName()
        ) {
            $stepsExecutionDto->setIsSuccessful(false);

            $stepsExecutionDto->setError(
                Error::createInternalError('Please check defined values of environment variables: ACCESS_TOKEN, ORGANIZATION_NAME and REPOSITORY_NAME.'),
            );
        }

        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     * @param \Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto $pullRequestDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function createPullRequest(StepsResponseDto $stepsExecutionDto, PullRequestDto $pullRequestDto): StepsResponseDto
    {
        try {
            $stepsExecutionDto = $this->validateCredentials($stepsExecutionDto);
            if (!$stepsExecutionDto->getIsSuccessful()) {
                return $stepsExecutionDto;
            }
            $organizationName = $this->configurationProvider->getOrganizationName();
            $repositoryName = $this->configurationProvider->getRepositoryName();
            $prClient = $this->gitHubClientFactory->getClient()->pr();

            $response = $prClient->create(
                $organizationName,
                $repositoryName,
                [
                    'base' => $pullRequestDto->getTargetBranch(),
                    'head' => $pullRequestDto->getSourceBranch(),
                    'title' => $pullRequestDto->getTitle(),
                    'body' => $pullRequestDto->getBody(),
                    'auto_merge' => $pullRequestDto->isAutoMerge(),
                ],
            );

            if (isset($response[static::HTML_URL_KEY])) {
                $stepsExecutionDto->addOutputMessage(
                    $this->outputMessageBuilder->buildOutputMessage($response[static::HTML_URL_KEY]),
                );
            }

            $reviewers = $this->configurationProvider->getPullRequestReviewers();
            if ($reviewers) {
                $prClient->reviewRequests()->create($organizationName, $repositoryName, $response[static::NUMBER_KEY], $reviewers);
            }

            return $stepsExecutionDto;
        } catch (RuntimeException $runtimeException) {
            return $stepsExecutionDto
                ->setIsSuccessful(false)
                ->setError(Error::createInternalError($runtimeException->getMessage()));
        }
    }

    /**
     * @param \Upgrade\Application\Dto\ValidatorViolationDto $blocker
     *
     * @return string
     */
    public function buildBlockerTextBlock(ValidatorViolationDto $blocker): string
    {
        return sprintf(
            '> [!IMPORTANT] %s> <b>%s.</b> %s',
            PHP_EOL,
            $blocker->getTitle(),
            $this->buildMessageWithTruncatedTrace($blocker->getMessage()),
        ) . PHP_EOL;
    }

    /**
     * @param string $message
     *
     * @return string
     */
    protected function buildMessageWithTruncatedTrace(string $message): string
    {
        $messageArray = explode(self::STRING_STACK_TRACE, $message);

        if (
            !$this->configurationProvider->isTruncateErrorTracesInPrsEnabled()
            || !isset($messageArray[0])
            || !isset($messageArray[1])
        ) {
            return $message;
        }

        $traceArray = explode(self::STRING_NUMBER_ONE, $messageArray[1]);

        if (!isset($traceArray[0])) {
            return $message;
        }

        return $messageArray[0]
            . $traceArray[0]
            . self::STRING_TRACE_TRUNCATED;
    }
}
