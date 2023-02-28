<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab;

use Gitlab\Client;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
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
        // Assert
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
        $gitLabClientFactory->method('getClient')->willReturn($this->createMock(Client::class));

        return $gitLabClientFactory;
    }
}
