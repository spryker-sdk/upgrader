<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\HttpRequestInterface;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponseInterface;
use Upgrader\UpgraderConfig;

class HttpCommunicator implements HttpCommunicatorInterface
{
    public const HTTP_HEADER_LIST = ['Content-Type' => 'application/json'];

    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $upgraderConfig;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $communicationClient;

    /**
     * @param \Upgrader\UpgraderConfig $upgraderConfig
     * @param \GuzzleHttp\Client $communicationClient
     */
    public function __construct(UpgraderConfig $upgraderConfig, Client $communicationClient)
    {
        $this->upgraderConfig = $upgraderConfig;
        $this->communicationClient = $communicationClient;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\HttpRequestInterface $request
     *
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponseInterface
     */
    public function send(HttpRequestInterface $request): HttpResponseInterface
    {
        $communicationRequest = $this->createCommunicationRequest($request);
        $communicationResponse = $this->communicationClient->send($communicationRequest);
        $response = $this->httpResponseBuilder($request, $communicationResponse);

        return $response;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\HttpRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $communicationResponse
     *
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponseInterface
     */
    protected function httpResponseBuilder(
        HttpRequestInterface $request,
        ResponseInterface $communicationResponse
    ): HttpResponseInterface {
        $responseStream = $communicationResponse->getBody();
        $responseStream->seek(0);
        $length = $responseStream->getSize();
        $body = $responseStream->read($length);

        /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponseInterface $response */
        $responseClass = $request->getResponseClass();
        $response = new $responseClass($communicationResponse->getStatusCode(), $body);

        return $response;
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return $this->upgraderConfig->getReleaseAppUrl();
    }

    /**
     * @param \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\HttpRequestInterface $request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function createCommunicationRequest(HttpRequestInterface $request): Request
    {
        return new Request(
            $request->getMethod(),
            $this->getBaseUrl() . $request->getEndpoint(),
            self::HTTP_HEADER_LIST,
            $request->getBody()
        );
    }
}
