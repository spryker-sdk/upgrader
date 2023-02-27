<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\GitLab;

use Gitlab\Client;
use Gitlab\HttpClient\Builder;
use Psr\Http\Client\ClientInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class GitLabClientFactory
{
    /**
     * @var \Gitlab\Client|null
     */
    protected ?Client $client = null;

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
     * @return \Gitlab\Client
     */
    public function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    /**
     * @return \Gitlab\Client
     */
    protected function createClient(): Client
    {
        $builder = new Builder($this->httpClient);
        $client = new Client($builder);
        $client->authenticate($this->configurationProvider->getAccessToken(), Client::AUTH_HTTP_TOKEN);

        if ($this->configurationProvider->getSourceCodeProviderUrl()) {
            $client->setUrl($this->configurationProvider->getSourceCodeProviderUrl());
        }

        return $client;
    }
}
