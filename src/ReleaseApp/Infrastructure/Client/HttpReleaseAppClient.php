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
    protected HttpRequestBuilderInterface $requestBuilder;

    protected HttpResponseBuilderInterface $responseBuilder;

    protected HttpRequestExecutorInterface $requestExecutor;

    public function __construct(
        HttpRequestBuilderInterface $requestBuilder,
        HttpResponseBuilderInterface $responseBuilder,
        HttpRequestExecutorInterface $requestExecutor
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->responseBuilder = $responseBuilder;
        $this->requestExecutor = $requestExecutor;
    }

    public function getUpgradeInstructions(UpgradeInstructionsRequest $instructionsRequest): UpgradeInstructions
    {
        /** @var \ReleaseApp\Domain\Entities\UpgradeInstructions $response */
        $response = $this->getResponse(new HttpUpgradeInstructionsRequest($instructionsRequest));

        return $response;
    }

    public function getUpgradeReleaseGroupInstruction(UpgradeReleaseGroupInstructionsRequest $releaseGroupRequest): UpgradeInstruction
    {
        /** @var \ReleaseApp\Domain\Entities\UpgradeInstruction $response */
        $response = $this->getResponse(new HttpUpgradeReleaseGroupInstructionsRequest($releaseGroupRequest));

        return $response;
    }

    protected function getResponse(HttpRequestInterface $request): ResponseInterface
    {
        $guzzleRequest = $this->requestBuilder->createRequest($request);
        $guzzleResponse = $this->requestExecutor->execute($guzzleRequest);
        $response = $this->responseBuilder->createHttpResponse($request, $guzzleResponse);

        return $response;
    }
}
