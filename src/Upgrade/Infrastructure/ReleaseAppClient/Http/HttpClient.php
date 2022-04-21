<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http;

use Upgrade\Infrastructure\ReleaseAppClient\Http\Builder\HttpRequestBuilderInterface;
use Upgrade\Infrastructure\ReleaseAppClient\Http\Builder\HttpResponseBuilderInterface;

class HttpClient implements HttpClientInterface
{
    /**
     * @var \Upgrade\Infrastructure\ReleaseAppClient\Http\Builder\HttpRequestBuilderInterface
     */
    protected $guzzleRequestBuilder;

    /**
     * @var \Upgrade\Infrastructure\ReleaseAppClient\Http\Builder\HttpResponseBuilderInterface
     */
    protected $responseBuilder;

    /**
     * @var \Upgrade\Infrastructure\ReleaseAppClient\Http\HttpRequestExecutorInterface
     */
    protected $requestExecutor;

    /**
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\Builder\HttpRequestBuilderInterface $guzzleRequestBuilder
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\Builder\HttpResponseBuilderInterface $responseBuilder
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\HttpRequestExecutorInterface $requestExecutor
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
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\HttpRequestInterface $request
     *
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\HttpResponseInterface
     */
    public function send(HttpRequestInterface $request): HttpResponseInterface
    {
        $guzzleRequest = $this->guzzleRequestBuilder->createGuzzleRequest($request);
        $guzzleResponse = $this->requestExecutor->requestExecute($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
