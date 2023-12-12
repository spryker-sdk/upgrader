<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\Report\Sender;

use DateTimeImmutable;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Infrastructure\Report\Dto\ReportDto;
use Upgrade\Infrastructure\Report\Dto\ReportMetadataDto;
use Upgrade\Infrastructure\Report\Dto\ReportPayloadDto;
use Upgrade\Infrastructure\Report\Sender\RemoteEndpointJsonReportSender;

class RemoteEndpointJsonReportSenderTest extends TestCase
{
    /**
     * @return \Upgrade\Infrastructure\Report\Dto\ReportMetadataDto
     */
    protected function createReportMetadataDto(): ReportMetadataDto
    {
        return new ReportMetadataDto(
            'organisation_name',
            'project_name',
            'project_version',
            'github',
            'test',
            'report_id',
            1,
            new DateTimeImmutable(),
        );
    }

    /**
     * @return void
     */
    public function testSendReport(): void
    {
        // Create mock instances for dependencies
        $httpClient = $this->createMock(ClientInterface::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $configurationProvider = $this->createMock(ConfigurationProviderInterface::class);

        // Create an instance of ReportDto (or use a mock)
        $reportDto = new ReportDto(
            'your_report_name',
            1,
            'scope',
            new DateTimeImmutable(),
            new ReportPayloadDto(),
            $this->createReportMetadataDto(),
        );

        // Configure the serializer mock
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($reportDto, 'json')
            ->willReturn('{"data": "serialized_report_data"}');

        // Configure the configuration provider mock
        $configurationProvider->expects($this->once())
            ->method('getReportSendAuthToken')
            ->willReturn('your_auth_token');

        // Configure the http client mock
        $httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'your_endpoint_url',
                [
                    'query' => ['token' => 'your_auth_token'],
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => '{"data": "serialized_report_data"}',
                    'timeout' => 10, // Example value, adjust as needed
                    'connect_timeout' => 5, // Example value, adjust as needed
                ],
            );

        // Create an instance of the RemoteEndpointJsonReportSender and inject mocks
        $reportSender = new RemoteEndpointJsonReportSender(
            $httpClient,
            $serializer,
            $configurationProvider,
            'your_endpoint_url',
            10, // Example value, adjust as needed
            5, // Example value, adjust as needed
        );

        // Call the send method
        $reportSender->send($reportDto);
    }
}
