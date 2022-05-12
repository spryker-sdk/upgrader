<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Client;

use ReleaseApp\Domain\Client\ClientInterface;
use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Response\ResponseInterface;
use ReleaseApp\Domain\Entities\UpgradeAnalysis;
use ReleaseApp\Domain\Entities\UpgradeInstructions;
use ReleaseApp\Infrastructure\Client\Builder\HttpRequestBuilderInterface;
use ReleaseApp\Infrastructure\Client\Builder\HttpResponseBuilderInterface;
use ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface;
use ReleaseApp\Infrastructure\Client\Request\HttpUpgradeAnalysisHttpRequest;
use ReleaseApp\Infrastructure\Client\Request\HttpUpgradeInstructionsRequest;

class HttpClient implements ClientInterface
{
    /**
     * @var \ReleaseApp\Infrastructure\Client\Builder\HttpRequestBuilderInterface
     */
    protected HttpRequestBuilderInterface $requestBuilder;

    /**
     * @var \ReleaseApp\Infrastructure\Client\Builder\HttpResponseBuilderInterface
     */
    protected HttpResponseBuilderInterface $responseBuilder;

    /**
     * @var \ReleaseApp\Infrastructure\Client\HttpRequestExecutorInterface
     */
    protected HttpRequestExecutorInterface $requestExecutor;

    /**
     * @param \ReleaseApp\Infrastructure\Client\Builder\HttpRequestBuilderInterface $requestBuilder
     * @param \ReleaseApp\Infrastructure\Client\Builder\HttpResponseBuilderInterface $responseBuilder
     * @param \ReleaseApp\Infrastructure\Client\HttpRequestExecutorInterface $requestExecutor
     */
    public function __construct(
        HttpRequestBuilderInterface $requestBuilder,
        HttpResponseBuilderInterface $responseBuilder,
        HttpRequestExecutorInterface $requestExecutor
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->responseBuilder = $responseBuilder;
        $this->requestExecutor = $requestExecutor;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest $instructionsRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructions
     */
    public function getUpgradeInstructions(UpgradeInstructionsRequest $instructionsRequest): UpgradeInstructions
    {
        /** @var \ReleaseApp\Domain\Entities\UpgradeInstructions $response */
        $response = $this->getResponse(new HttpUpgradeInstructionsRequest($instructionsRequest));

        return $response;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeAnalysis
     */
    public function getUpgradeAnalysis(UpgradeAnalysisRequest $upgradeAnalysisRequest): UpgradeAnalysis
    {
        /** @var \ReleaseApp\Domain\Entities\UpgradeAnalysis $response */
        $response = $this->getResponse(new HttpUpgradeAnalysisHttpRequest($upgradeAnalysisRequest));

        return $response;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface $request
     *
     * @return \ReleaseApp\Domain\Client\Response\ResponseInterface
     */
    protected function getResponse(HttpRequestInterface $request): ResponseInterface
    {
        $guzzleRequest = $this->requestBuilder->createRequest($request);
        $guzzleResponse = $this->requestExecutor->send($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
