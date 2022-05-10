<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository\Builder;

use Psr\Http\Message\ResponseInterface;
use ReleaseApp\Domain\Client\Response\ResponseInterface as DomainResponse;
use ReleaseApp\Infrastructure\Repository\Request\HttpRequestInterface;

class HttpResponseBuilder implements HttpResponseBuilderInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Repository\Request\HttpRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $guzzleResponse
     *
     * @return \ReleaseApp\Domain\Client\Response\ResponseInterface
     */
    public function createHttpResponse(
        HttpRequestInterface $request,
        ResponseInterface $guzzleResponse
    ): DomainResponse {
        /** @var \ReleaseApp\Domain\Client\Response\ResponseInterface $responseClass */
        $responseClass = $request->getDomainRequest()->getResponseClass();
        $response = new $responseClass($guzzleResponse->getStatusCode(), $this->getBody($guzzleResponse));

        return $response;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $guzzleResponse
     *
     * @return string
     */
    protected function getBody(ResponseInterface $guzzleResponse): string
    {
        $responseStream = $guzzleResponse->getBody();
        $responseStream->seek(0);
        $length = $responseStream->getSize();
        $body = $responseStream->read((int)$length);

        return $body;
    }
}
