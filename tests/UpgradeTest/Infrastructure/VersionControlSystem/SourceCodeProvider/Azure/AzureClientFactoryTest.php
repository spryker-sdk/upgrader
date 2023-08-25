<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure;

use SprykerAzure\Client\Client as AzureClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure\AzureClientFactory;

class AzureClientFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetClient(): void
    {
        $accessToken = 'your_access_token';
        $sourceCodeProviderUrl = 'https://your.azure.url';

        // Create mock for ConfigurationProvider
        $configurationProviderMock = $this->getMockBuilder(ConfigurationProvider::class)
            ->getMock();
        $configurationProviderMock->method('getAccessToken')->willReturn($accessToken);
        $configurationProviderMock->method('getSourceCodeProviderUrl')->willReturn($sourceCodeProviderUrl);

        // Create mock for ClientInterface
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)
            ->getMock();

        $gitLabClientFactory = new AzureClientFactory($configurationProviderMock, $httpClientMock);

        // Test the first call to getClient() - should create a new instance
        $client = $gitLabClientFactory->getClient();
        $this->assertInstanceOf(AzureClient::class, $client);

        // Test the second call to getClient() - should return the same instance
        $sameClient = $gitLabClientFactory->getClient();
        $this->assertSame($client, $sameClient);
    }

    /**
     * @return void
     */
    public function testCreateClient(): void
    {
        $accessToken = 'your_access_token';
        $sourceCodeProviderUrl = 'https://your.azure.url';

        // Create mock for ConfigurationProvider
        $configurationProviderMock = $this->getMockBuilder(ConfigurationProvider::class)
            ->getMock();
        $configurationProviderMock->method('getAccessToken')->willReturn($accessToken);
        $configurationProviderMock->method('getSourceCodeProviderUrl')->willReturn($sourceCodeProviderUrl);

        // Create mock for ClientInterface
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)
            ->getMock();

        $gitLabClientFactory = new AzureClientFactory($configurationProviderMock, $httpClientMock);

        // Call createClient
        $client = $gitLabClientFactory->getClient();

        // Assertions
        $this->assertInstanceOf(AzureClient::class, $client);
    }
}
