<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Sender;

use GuzzleHttp\ClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Infrastructure\Report\Dto\ReportDto;

class RemoteEndpointJsonReportSender implements ReportSenderInterface
{
    protected ClientInterface $httpClient;

    protected SerializerInterface $serializer;

    protected ConfigurationProviderInterface $configurationProvider;

    protected string $endpointUrl;

    protected int $timeout;

    protected int $connectionTimeout;

    public function __construct(
        ClientInterface $httpClient,
        SerializerInterface $serializer,
        ConfigurationProviderInterface $configurationProvider,
        string $endpointUrl,
        int $timeout,
        int $connectionTimeout
    ) {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->endpointUrl = $endpointUrl;
        $this->timeout = $timeout;
        $this->connectionTimeout = $connectionTimeout;
        $this->configurationProvider = $configurationProvider;
    }

    public function send(ReportDto $reportDto): void
    {
        if (!$this->configurationProvider->isReportingEnabled()) {
            return;
        }

        $this->httpClient->request(
            'POST',
            $this->endpointUrl,
            [
                'query' => ['token' => $this->configurationProvider->getReportSendAuthToken()],
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $this->serializer->serialize($reportDto, 'json'),
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connectionTimeout,
            ],
        );
    }
}
