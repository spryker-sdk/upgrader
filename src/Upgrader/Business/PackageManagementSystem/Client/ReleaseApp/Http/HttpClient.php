<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http;

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
    protected $responseBuilder;

    /**
     * @var \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestExecutorInterface
     */
    protected $requestExecutor;

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpRequestBuilderInterface $guzzleRequestBuilder
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpResponseBuilderInterface $responseBuilder
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestExecutorInterface $requestExecutor
     */
    public function __construct(
        HttpRequestBuilderInterface $guzzleRequestBuilder,
        HttpResponseBuilderInterface $responseBuilder,
        HttpRequestExecutorInterface $requestExecutor
    ) {
        $this->guzzleRequestBuilder = $guzzleRequestBuilder;
        $this->responseBuilder = $responseBuilder;
        $this->requestExecutor = $requestExecutor;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestInterface $request
     *
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpResponseInterface
     */
    public function send(HttpRequestInterface $request): HttpResponseInterface
    {
        $guzzleRequest = $this->guzzleRequestBuilder->createGuzzleRequest($request);
        $guzzleResponse = $this->requestExecutor->requestExecute($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
