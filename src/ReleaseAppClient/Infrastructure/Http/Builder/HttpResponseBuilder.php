<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Infrastructure\Http\Builder;

use Psr\Http\Message\ResponseInterface;
use ReleaseAppClient\Domain\Http\HttpRequestInterface;
use ReleaseAppClient\Domain\Http\HttpResponseInterface;

class HttpResponseBuilder implements HttpResponseBuilderInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Http\HttpRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $guzzleResponse
     *
     * @return \ReleaseAppClient\Domain\Http\HttpResponseInterface
     */
    public function createHttpResponse(
        HttpRequestInterface $request,
        ResponseInterface $guzzleResponse
    ): HttpResponseInterface {
        /** @var \ReleaseAppClient\Domain\Http\HttpResponseInterface $responseClass */
        $responseClass = $request->getResponseClass();
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
