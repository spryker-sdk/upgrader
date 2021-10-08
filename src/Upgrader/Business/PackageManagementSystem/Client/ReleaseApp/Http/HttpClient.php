<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpRequestBuilderInterface;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpResponseBuilderInterface;

class HttpClient implements HttpClientInterface
{
    /**
     * @var \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpRequestBuilderInterface
     */
    protected $guzzleRequestBuilder;

    /**
     * @var \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpResponseBuilderInterface
     */
    protected $httpResponseBuilder;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzleClient;

    /**
     * @param Builder\HttpRequestBuilderInterface $guzzleRequestBuilder
     * @param Builder\HttpResponseBuilderInterface $httpResponseBuilder
     * @param \GuzzleHttp\Client $guzzleClient
     */
    public function __construct(
        HttpRequestBuilderInterface $guzzleRequestBuilder,
        HttpResponseBuilderInterface $httpResponseBuilder,
        Client $guzzleClient
    ) {
        $this->guzzleRequestBuilder = $guzzleRequestBuilder;
        $this->httpResponseBuilder = $httpResponseBuilder;
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestInterface $request
     *
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpResponseInterface
     */
    public function send(HttpRequestInterface $request): HttpResponseInterface
    {
        $guzzleRequest = $this->guzzleRequestBuilder->createGuzzleRequest($request);
        $guzzleResponse = $this->requestExecute($guzzleRequest);
        $response = $this->httpResponseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }

    protected function requestExecute(RequestInterface $request): ResponseInterface
    {
        $iterration = 0;
        $exception = null;
        $guzzleResponse = null;

        while ($iterration < 5 && $guzzleResponse == null) {
            try {
                $iterration++;
                $guzzleResponse = $this->guzzleClient->send($request);
            }catch (ServerException $currentException) {
                $exception = $currentException;
                if ($exception->getCode() != 503) {
                    throw $exception;
                }
            }
            sleep(5);
        }

        if(!$guzzleResponse) {
            throw $exception;
        }


        var_dump($guzzleResponse->getStatusCode());
        exit(15);

        return $guzzleResponse;
    }


}
