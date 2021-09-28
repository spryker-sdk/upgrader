<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder;

use Psr\Http\Message\ResponseInterface;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestInterface;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpResponseInterface;

class HttpResponseBuilder implements HttpResponseBuilderInterface
{
    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $guzzleResponse
     *
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpResponseInterface
     */
    public function createHttpResponse(
        HttpRequestInterface $request,
        ResponseInterface $guzzleResponse
    ): HttpResponseInterface {
        /** @var \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpResponseInterface $responseClass */
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
