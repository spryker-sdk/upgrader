<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository;

use ReleaseApp\Domain\Repository\ResponseRepositoryInterface;
use ReleaseApp\Domain\Entities\RequestInterface;
use ReleaseApp\Domain\Entities\ResponseInterface;
use ReleaseApp\Infrastructure\Repository\Builder\HttpRequestBuilderInterface;
use ReleaseApp\Infrastructure\Repository\Builder\HttpResponseBuilderInterface;

class HttpResponseRepository implements ResponseRepositoryInterface
{
    /**
     * @var \ReleaseApp\Infrastructure\Repository\Builder\HttpRequestBuilderInterface
     */
    protected $guzzleRequestBuilder;

    /**
     * @var \ReleaseApp\Infrastructure\Repository\Builder\HttpResponseBuilderInterface
     */
    protected $responseBuilder;

    /**
     * @var \ReleaseApp\Infrastructure\Repository\HttpRequestExecutorInterface
     */
    protected $requestExecutor;

    /**
     * @param \ReleaseApp\Infrastructure\Repository\Builder\HttpRequestBuilderInterface $guzzleRequestBuilder
     * @param \ReleaseApp\Infrastructure\Repository\Builder\HttpResponseBuilderInterface $responseBuilder
     * @param \ReleaseApp\Infrastructure\Repository\HttpRequestExecutorInterface $requestExecutor
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
     * @param \ReleaseApp\Domain\Entities\RequestInterface $request
     *
     * @return \ReleaseApp\Domain\Entities\ResponseInterface
     */
    public function getResponse(RequestInterface $request): ResponseInterface
    {
        $guzzleRequest = $this->guzzleRequestBuilder->createGuzzleRequest($request);
        $guzzleResponse = $this->requestExecutor->requestExecute($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
