<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http\Builder;

use Psr\Http\Message\ResponseInterface;
use Upgrade\Infrastructure\ReleaseAppClient\Http\HttpRequestInterface;
use Upgrade\Infrastructure\ReleaseAppClient\Http\HttpResponseInterface;

interface HttpResponseBuilderInterface
{
    /**
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\HttpRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $guzzleResponse
     *
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\HttpResponseInterface
     */
    public function createHttpResponse(HttpRequestInterface $request, ResponseInterface $guzzleResponse): HttpResponseInterface;
}
