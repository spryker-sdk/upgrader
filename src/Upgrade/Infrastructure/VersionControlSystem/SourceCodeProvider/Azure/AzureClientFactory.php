<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure;

use Psr\Http\Client\ClientInterface;
use SprykerAzure\Client\ClientBuilder;
use SprykerAzure\Client\ClientInterface as AzureClientInterface;
use SprykerAzure\Client\Plugin\Request\PersonalAccessTokenAuthPlugin;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class AzureClientFactory
{
    protected ?AzureClientInterface $client = null;

    protected ConfigurationProvider $configurationProvider;

    protected ClientInterface $httpClient;

    public function __construct(ConfigurationProvider $configurationProvider, ClientInterface $httpClient)
    {
        $this->configurationProvider = $configurationProvider;
        $this->httpClient = $httpClient;
    }

    public function getClient(): AzureClientInterface
    {
        if ($this->client === null) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    protected function createClient(): AzureClientInterface
    {
        $clientBuilder = new ClientBuilder();
        $clientBuilder->setHttpClient($this->httpClient);
        $clientBuilder->addRequestPlugin(new PersonalAccessTokenAuthPlugin($this->configurationProvider->getAccessToken()));

        return $clientBuilder->getClient();
    }
}
