<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure;

use PHPUnit\Framework\TestCase;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzureSourceCodeProvider;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzureClientFactory;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzurePullRequestDescriptionNormalizer;

class AzureSourceCodeProviderTest extends TestCase
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    private ConfigurationProvider $configurationProviderMock;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzureClientFactory
     */
    private AzureClientFactory $azureClientFactoryMock;

    /**
     * @var \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzurePullRequestDescriptionNormalizer
     */
    private AzurePullRequestDescriptionNormalizer $descriptionNormalizerMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->configurationProviderMock = $this->createMock(ConfigurationProvider::class);
        $this->azureClientFactoryMock = $this->createMock(AzureClientFactory::class);
        $this->descriptionNormalizerMock = $this->createMock(AzurePullRequestDescriptionNormalizer::class);
    }

    /**
     * @return array<array<mixed>>
     */
    public function validateCredentialsDataProvider(): array
    {
        return [
            // Invalid credentials
            ['', '', '', '', '', '', "ACCESS_TOKEN and ORGANIZATION_NAME should be set\nPROJECT_NAME or PROJECT_ID should be set\nREPOSITORY_NAME or REPOSITORY_ID should be set"],
            ['access_token', '', '', '', '', '', "ACCESS_TOKEN and ORGANIZATION_NAME should be set\nPROJECT_NAME or PROJECT_ID should be set\nREPOSITORY_NAME or REPOSITORY_ID should be set"],
            ['access_token', 'org_name', '', '', '', '', "PROJECT_NAME or PROJECT_ID should be set\nREPOSITORY_NAME or REPOSITORY_ID should be set"],
            ['access_token', 'org_name', 'project_name', '', '', '', 'REPOSITORY_NAME or REPOSITORY_ID should be set'],
            ['access_token', 'org_name', 'project_name', 'project_id', '', '', ''],
            ['access_token', 'org_name', '', 'project_id', 'repo_name', '', ''],
            ['access_token', 'org_name', '', 'project_id', '', 'repo_id', ''],
        ];
    }

    /**
     * @dataProvider validateCredentialsDataProvider
     *
     * @param string $accessToken
     * @param string $orgName
     * @param string $projectName
     * @param string $projectId
     * @param string $repoName
     * @param string $repoId
     * @param string $expectedError
     *
     * @return void
     */
    public function testValidateCredentials(string $accessToken, string $orgName, string $projectName, string $projectId, string $repoName, string $repoId, string $expectedError): void
    {
        // Set up mocks and dependencies

        $azureSourceCodeProvider = new AzureSourceCodeProvider(
            $this->configurationProviderMock,
            $this->azureClientFactoryMock,
            $this->descriptionNormalizerMock,
        );

        // Configure the mocks
        $this->configurationProviderMock->method('getAccessToken')->willReturn($accessToken);
        $this->configurationProviderMock->method('getOrganizationName')->willReturn($orgName);
        $this->configurationProviderMock->method('getProjectName')->willReturn($projectName);
        $this->configurationProviderMock->method('getProjectId')->willReturn($projectId);
        $this->configurationProviderMock->method('getRepositoryName')->willReturn($repoName);
        $this->configurationProviderMock->method('getRepositoryId')->willReturn($repoId);

        // Create mock DTO
        $stepsExecutionDto = new StepsResponseDto();

        if ($expectedError) {
            $stepsExecutionDto = $azureSourceCodeProvider->validateCredentials($stepsExecutionDto);
            $this->assertFalse($stepsExecutionDto->getIsSuccessful());
            $this->assertInstanceOf(Error::class, $stepsExecutionDto->getError());
            $this->assertEquals($expectedError, $stepsExecutionDto->getError()->getErrorMessage());
        } else {
            $this->assertTrue($stepsExecutionDto->getIsSuccessful());
            $this->assertNull($stepsExecutionDto->getError());
        }
    }
}
