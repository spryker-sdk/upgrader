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
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected ClientInterface $httpClient;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var string
     */
    protected string $endpointUrl;

    /**
     * @var int
     */
    protected int $timeout;

    /**
     * @var int
     */
    protected int $connectionTimeout;

    /**
     * @param \GuzzleHttp\ClientInterface $httpClient
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param string $endpointUrl
     * @param int $timeout
     * @param int $connectionTimeout
     */
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

    /**
     * @param \Upgrade\Infrastructure\Report\Dto\ReportDto $reportDto
     *
     * @return void
     */
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
