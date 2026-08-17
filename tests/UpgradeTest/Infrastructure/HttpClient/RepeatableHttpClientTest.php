<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\HttpClient;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Upgrade\Infrastructure\Exception\RemoteServerUnreachableException;
use Upgrade\Infrastructure\HttpClient\RepeatableHttpClient;

class RepeatableHttpClientTest extends TestCase
{
    public function testSendRequestShouldThrowExceptionWhenServerUnreachable(): void
    {
        // Arrange & Assert
        $clientMock = $this->createUnreachableServerClientMock();
        $repeatableHttpClientMock = new RepeatableHttpClient($clientMock, 1, 0);
        $this->expectException(RemoteServerUnreachableException::class);
        $request = $this->createRequestMock();

        // Act
        $repeatableHttpClientMock->sendRequest($request);
    }

    public function testSendRequestShouldThrowExceptionWhen500ResponseReturned(): void
    {
        // Arrange & Assert
        $response = $this->createResponseMock(500);
        $clientMock = $this->createServerClientMock($response);
        $repeatableHttpClientMock = new RepeatableHttpClient($clientMock, 1, 0);
        $this->expectException(RemoteServerUnreachableException::class);
        $request = $this->createRequestMock();

    // Act
        $repeatableHttpClientMock->sendRequest($request);
    }

    public function testSendRequestShouldReturnResponseWhenSuccessRequestProcessed(): void
    {
        // Arrange
        $response = $this->createResponseMock(200);
        $clientMock = $this->createServerClientMock($response);
        $repeatableHttpClientMock = new RepeatableHttpClient($clientMock, 1, 0);
        $request = $this->createRequestMock();

        // Act
        $receivedResponse = $repeatableHttpClientMock->sendRequest($request);

        // Assert
        $this->assertSame($response, $receivedResponse);
    }

    protected function createUnreachableServerClientMock(): ClientInterface
    {
        $client = $this->createMock(ClientInterface::class);
        $client
        ->method('sendRequest')
        ->willThrowException(
            $this->createMock(NetworkExceptionInterface::class),
        );

        return $client;
    }

    protected function createServerClientMock(ResponseInterface $response): ClientInterface
    {
        $client = $this->createMock(ClientInterface::class);
        $client
        ->method('sendRequest')
        ->willReturn($response);

        return $client;
    }

    protected function createResponseMock(int $statusCode): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);

        return $response;
    }

    protected function createRequestMock(): RequestInterface
    {
        return $this->createMock(RequestInterface::class);
    }
}
