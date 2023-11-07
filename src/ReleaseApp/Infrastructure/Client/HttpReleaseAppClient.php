<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Client;

use ReleaseApp\Domain\Client\ReleaseAppClientInterface;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Domain\Client\Response\ResponseInterface;
use ReleaseApp\Domain\Entities\UpgradeInstruction;
use ReleaseApp\Domain\Entities\UpgradeInstructions;
use ReleaseApp\Infrastructure\Client\Builder\HttpRequestBuilderInterface;
use ReleaseApp\Infrastructure\Client\Builder\HttpResponseBuilderInterface;
use ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface;
use ReleaseApp\Infrastructure\Client\Request\HttpUpgradeInstructionsRequest;
use ReleaseApp\Infrastructure\Client\Request\HttpUpgradeReleaseGroupInstructionsRequest;

class HttpReleaseAppClient implements ReleaseAppClientInterface
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
     * @param \ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest $releaseGroupRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstruction
     */
    public function getUpgradeReleaseGroupInstruction(UpgradeReleaseGroupInstructionsRequest $releaseGroupRequest): UpgradeInstruction
    {
        /** @var \ReleaseApp\Domain\Entities\UpgradeInstruction $response */
        $response = $this->getResponse(new HttpUpgradeReleaseGroupInstructionsRequest($releaseGroupRequest));

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
        $guzzleResponse = $this->requestExecutor->execute($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
