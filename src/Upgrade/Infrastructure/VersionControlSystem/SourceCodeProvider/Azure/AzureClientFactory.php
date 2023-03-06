<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure;

use Psr\Http\Client\ClientInterface;
use SprykerAzure\Client\Builder\Plugin\PersonalAccessTokenAuthPlugin;
use SprykerAzure\Client\ClientFactory;
use SprykerAzure\Client\ClientInterface as AzureClientInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class AzureClientFactory
{
    /**
     * @var \SprykerAzure\Client\ClientInterface|null
     */
    protected ?AzureClientInterface $client = null;

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    protected ClientInterface $httpClient;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param \Psr\Http\Client\ClientInterface $httpClient
     */
    public function __construct(ConfigurationProvider $configurationProvider, ClientInterface $httpClient)
    {
        $this->configurationProvider = $configurationProvider;
        $this->httpClient = $httpClient;
    }

    /**
     * @return \SprykerAzure\Client\ClientInterface
     */
    public function getClient(): AzureClientInterface
    {
        if ($this->client === null) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    /**
     * @return \SprykerAzure\Client\ClientInterface
     */
    protected function createClient(): AzureClientInterface
    {
        $clientFactory = new ClientFactory();
        $requestBuilder = $clientFactory->getDefaultRequestBuilder();
        $requestBuilder->addRequestPlugin(new PersonalAccessTokenAuthPlugin($this->configurationProvider->getAccessToken()));

        return $clientFactory->createClient($requestBuilder, null, $this->httpClient);
    }
}
