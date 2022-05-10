<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository;

use ReleaseApp\Domain\Client\ClientInterface;
use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Response\ResponseInterface;
use ReleaseApp\Domain\Entities\UpgradeAnalysis;
use ReleaseApp\Domain\Entities\UpgradeInstructions;
use ReleaseApp\Infrastructure\Repository\Builder\HttpRequestBuilderInterface;
use ReleaseApp\Infrastructure\Repository\Builder\HttpResponseBuilderInterface;
use ReleaseApp\Infrastructure\Repository\Request\HttpRequestInterface;
use ReleaseApp\Infrastructure\Repository\Request\HttpUpgradeAnalysisHttpRequest;
use ReleaseApp\Infrastructure\Repository\Request\HttpUpgradeInstructionsRequest;

class HttpClient implements ClientInterface
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
     * @param \ReleaseApp\Infrastructure\Repository\Request\HttpRequestInterface $request
     *
     * @return \ReleaseApp\Domain\Client\Response\ResponseInterface
     */
    protected function getResponse(HttpRequestInterface $request): ResponseInterface
    {
        $guzzleRequest = $this->guzzleRequestBuilder->createGuzzleRequest($request);
        $guzzleResponse = $this->requestExecutor->requestExecute($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
