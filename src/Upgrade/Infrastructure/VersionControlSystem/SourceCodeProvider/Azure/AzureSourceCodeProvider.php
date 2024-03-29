<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure;

use InvalidArgumentException;
use SprykerAzure\Api\PullRequestApi\PullRequestData;
use SprykerAzure\Api\RepositoryPath;
use Throwable;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\Dto\PullRequestDto;
use Upgrade\Infrastructure\VersionControlSystem\Generator\OutputMessageBuilder;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface;

class AzureSourceCodeProvider implements SourceCodeProviderInterface
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzureClientFactory
     */
    protected AzureClientFactory $azureClientFactory;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzurePullRequestDescriptionNormalizer
     */
    protected AzurePullRequestDescriptionNormalizer $descriptionNormalizer;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\Generator\OutputMessageBuilder
     */
    protected OutputMessageBuilder $outputMessageBuilder;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzureClientFactory $azureClientFactory
     * @param \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzurePullRequestDescriptionNormalizer $descriptionNormalizer
     * @param \Upgrade\Infrastructure\VersionControlSystem\Generator\OutputMessageBuilder $outputMessageBuilder
     */
    public function __construct(
        ConfigurationProvider $configurationProvider,
        AzureClientFactory $azureClientFactory,
        AzurePullRequestDescriptionNormalizer $descriptionNormalizer,
        OutputMessageBuilder $outputMessageBuilder
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->azureClientFactory = $azureClientFactory;
        $this->descriptionNormalizer = $descriptionNormalizer;
        $this->outputMessageBuilder = $outputMessageBuilder;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return ConfigurationProvider::AZURE_SOURCE_CODE_PROVIDER;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function validateCredentials(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $violations = [];

        if (!$this->configurationProvider->getAccessToken() || !$this->configurationProvider->getOrganizationName()) {
            $violations[] = 'ACCESS_TOKEN and ORGANIZATION_NAME should be set';
        }

        if (!$this->configurationProvider->getProjectName() && !$this->configurationProvider->getProjectId()) {
            $violations[] = 'PROJECT_NAME or PROJECT_ID should be set';
        }

        if (!$this->configurationProvider->getRepositoryName() && !$this->configurationProvider->getRepositoryId()) {
            $violations[] = 'REPOSITORY_NAME or REPOSITORY_ID should be set';
        }

        if (count($violations) > 0) {
            $stepsExecutionDto->setIsSuccessful(false);

            $stepsExecutionDto->setError(
                Error::createInternalError(implode(PHP_EOL, $violations)),
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
    public function createPullRequest(
        StepsResponseDto $stepsExecutionDto,
        PullRequestDto $pullRequestDto
    ): StepsResponseDto {
        try {
            $repositoryId = $this->getRepositoryId();
            $projectName = $this->configurationProvider->getProjectName() ?: $this->configurationProvider->getProjectId();

            $response = $this->azureClientFactory->getClient()->getPullRequestApi()->createPullRequest(
                new RepositoryPath(
                    $this->configurationProvider->getOrganizationName(),
                    $projectName,
                    $repositoryId,
                ),
                new PullRequestData(
                    $pullRequestDto->getTitle(),
                    $pullRequestDto->getTargetBranch(),
                    $pullRequestDto->getSourceBranch(),
                    $this->descriptionNormalizer->normalize((string)$pullRequestDto->getBody(), $stepsExecutionDto->getReportId()),
                ),
            );

            $stepsExecutionDto->addOutputMessage(
                $this->outputMessageBuilder->buildOutputMessage($response['webUrl'] ?? ''),
            );
        } catch (Throwable $e) {
            $stepsExecutionDto->setIsSuccessful(false);

            $stepsExecutionDto->setError(
                Error::createInternalError($e->getMessage()),
            );
        }

        return $stepsExecutionDto;
    }

    /**
     * @param \Upgrade\Application\Dto\ValidatorViolationDto $blocker
     *
     * @return string
     */
    public function buildBlockerTextBlock(ValidatorViolationDto $blocker): string
    {
        return sprintf('> <b>%s.</b> %s <br>', $blocker->getTitle(), $blocker->getMessage() . PHP_EOL) . PHP_EOL;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function getRepositoryId(): string
    {
        $repositoryId = $this->configurationProvider->getRepositoryId();

        if ($repositoryId) {
            return $repositoryId;
        }

        $response = $this->azureClientFactory->getClient()->getRepositoryApi()->getRepositoryInfo(
            new RepositoryPath(
                $this->configurationProvider->getOrganizationName(),
                $this->configurationProvider->getProjectName() ?: $this->configurationProvider->getProjectId(),
                $this->configurationProvider->getRepositoryName(),
            ),
        );

        if (!isset($response['id'])) {
            throw new InvalidArgumentException(sprintf('Unable to find repository id in response: `%s`', var_export($response, true)));
        }

        return $response['id'];
    }
}
