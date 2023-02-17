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
    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return \Psr\Http\Client\ClientInterface
     */
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

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Psr\Http\Client\ClientInterface
     */
    protected function createServerClientMock(ResponseInterface $response): ClientInterface
    {
        $client = $this->createMock(ClientInterface::class);
        $client
            ->method('sendRequest')
            ->willReturn($response);

        return $client;
    }

    /**
     * @param int $statusCode
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function createResponseMock(int $statusCode): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);

        return $response;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function createRequestMock(): RequestInterface
    {
        return $this->createMock(RequestInterface::class);
    }
}
