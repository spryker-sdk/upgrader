<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub;

use GitHub\Client as GitHubClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitHub\GitHubClientFactory;

class GitHubClientFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetClient(): void
    {
        $accessToken = 'your_access_token';

        // Create mock for ConfigurationProvider
        $configurationProviderMock = $this->getMockBuilder(ConfigurationProvider::class)
            ->getMock();
        $configurationProviderMock->method('getAccessToken')->willReturn($accessToken);

        // Create mock for ClientInterface
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)
            ->getMock();

        $gitHubClientFactory = new GitHubClientFactory($configurationProviderMock, $httpClientMock);

        // Test the first call to getClient() - should create a new instance
        $client = $gitHubClientFactory->getClient();
        $this->assertInstanceOf(GitHubClient::class, $client);

        // Test the second call to getClient() - should return the same instance
        $sameClient = $gitHubClientFactory->getClient();
        $this->assertSame($client, $sameClient);
    }

    /**
     * @return void
     */
    public function testCreateClient(): void
    {
        $accessToken = 'your_access_token';

        // Create mock for ConfigurationProvider
        $configurationProviderMock = $this->getMockBuilder(ConfigurationProvider::class)
            ->getMock();
        $configurationProviderMock->method('getAccessToken')->willReturn($accessToken);

        // Create mock for ClientInterface
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)
            ->getMock();

        $gitHubClientFactory = new GitHubClientFactory($configurationProviderMock, $httpClientMock);

        // Call createClient
        $client = $gitHubClientFactory->getClient();

        // Assertions
        $this->assertInstanceOf(GitHubClient::class, $client);
    }
}
