<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Infrastructure\Http\Builder;

use Psr\Http\Message\ResponseInterface;
use ReleaseAppClient\Domain\Http\HttpRequestInterface;
use ReleaseAppClient\Domain\Http\HttpResponseInterface;

interface HttpResponseBuilderInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Http\HttpRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $guzzleResponse
     *
     * @return \ReleaseAppClient\Domain\Http\HttpResponseInterface
     */
    public function createHttpResponse(HttpRequestInterface $request, ResponseInterface $guzzleResponse): HttpResponseInterface;
}
