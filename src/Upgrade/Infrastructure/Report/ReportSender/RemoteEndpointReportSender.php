<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\ReportSender;

use GuzzleHttp\ClientInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Infrastructure\Report\Dto\ReportDto;
use Upgrade\Infrastructure\Report\ReportFormatter\ReportFormatterInterface;

class RemoteEndpointReportSender implements ReportSenderInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected ClientInterface $httpClient;

    /**
     * @var \Upgrade\Infrastructure\Report\ReportFormatter\ReportFormatterInterface
     */
    protected ReportFormatterInterface $reportFormatter;

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
    protected int $timeOut;

    /**
     * @var int
     */
    protected int $connectionTimeout;

    /**
     * @param \GuzzleHttp\ClientInterface $httpClient
     * @param \Upgrade\Infrastructure\Report\ReportFormatter\ReportFormatterInterface $reportFormatter
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param string $endpointUrl
     * @param int $timeOut
     * @param int $connectionTimeout
     */
    public function __construct(
        ClientInterface $httpClient,
        ReportFormatterInterface $reportFormatter,
        ConfigurationProviderInterface $configurationProvider,
        string $endpointUrl,
        int $timeOut,
        int $connectionTimeout
    ) {
        $this->httpClient = $httpClient;
        $this->reportFormatter = $reportFormatter;
        $this->endpointUrl = $endpointUrl;
        $this->timeOut = $timeOut;
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
        $this->httpClient->request(
            'POST',
            $this->endpointUrl,
            [
                'query' => ['token' => $this->configurationProvider->getReportSendAuthToken()],
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($this->reportFormatter->format($reportDto), JSON_THROW_ON_ERROR),
                'timeout' => $this->timeOut,
                'connect_timeout' => $this->connectionTimeout,
            ],
        );
    }
}
