<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository\Builder;

use Psr\Http\Message\ResponseInterface;
use ReleaseApp\Domain\Entities\RequestInterface;
use ReleaseApp\Domain\Entities\ResponseInterface;

class HttpResponseBuilder implements HttpResponseBuilderInterface
{
    /**
     * @param \ReleaseApp\Domain\Entities\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $guzzleResponse
     *
     * @return \ReleaseApp\Domain\Entities\ResponseInterface
     */
    public function createHttpResponse(
        RequestInterface  $request,
        ResponseInterface $guzzleResponse
    ): ResponseInterface {
        /** @var \ReleaseApp\Domain\Entities\ResponseInterface $responseClass */
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
