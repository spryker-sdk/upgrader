<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpRequestExecutorInterface
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function requestExecute(RequestInterface $request): ResponseInterface;
}
