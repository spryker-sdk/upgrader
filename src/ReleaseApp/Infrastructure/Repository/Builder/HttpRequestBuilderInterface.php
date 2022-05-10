<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository\Builder;

use GuzzleHttp\Psr7\Request;
use ReleaseApp\Domain\Client\Request\RequestInterface;
use ReleaseApp\Infrastructure\Repository\Request\HttpRequestInterface;

interface HttpRequestBuilderInterface
{
    /**
     * @param HttpRequestInterface $request
     * @return Request
     */
    public function createGuzzleRequest(HttpRequestInterface $request): Request;
}
