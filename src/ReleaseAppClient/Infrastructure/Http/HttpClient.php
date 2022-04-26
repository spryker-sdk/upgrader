<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Infrastructure\Http;

use ReleaseAppClient\Domain\Http\HttpClientInterface;
use ReleaseAppClient\Domain\Http\HttpRequestInterface;
use ReleaseAppClient\Domain\Http\HttpResponseInterface;
use ReleaseAppClient\Infrastructure\Http\Builder\HttpRequestBuilderInterface;
use ReleaseAppClient\Infrastructure\Http\Builder\HttpResponseBuilderInterface;

class HttpClient implements HttpClientInterface
{
    /**
     * @var \ReleaseAppClient\Infrastructure\Http\Builder\HttpRequestBuilderInterface
     */
    protected $guzzleRequestBuilder;

    /**
     * @var \ReleaseAppClient\Infrastructure\Http\Builder\HttpResponseBuilderInterface
     */
    protected $responseBuilder;

    /**
     * @var \ReleaseAppClient\Infrastructure\Http\HttpRequestExecutorInterface
     */
    protected $requestExecutor;

    /**
     * @param \ReleaseAppClient\Infrastructure\Http\Builder\HttpRequestBuilderInterface $guzzleRequestBuilder
     * @param \ReleaseAppClient\Infrastructure\Http\Builder\HttpResponseBuilderInterface $responseBuilder
     * @param \ReleaseAppClient\Infrastructure\Http\HttpRequestExecutorInterface $requestExecutor
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
     * @param \ReleaseAppClient\Domain\Http\HttpRequestInterface $request
     *
     * @return \ReleaseAppClient\Domain\Http\HttpResponseInterface
     */
    public function send(HttpRequestInterface $request): HttpResponseInterface
    {
        $guzzleRequest = $this->guzzleRequestBuilder->createGuzzleRequest($request);
        $guzzleResponse = $this->requestExecutor->requestExecute($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
