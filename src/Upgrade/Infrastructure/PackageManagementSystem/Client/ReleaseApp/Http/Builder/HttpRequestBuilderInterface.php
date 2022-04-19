<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\Builder;

use GuzzleHttp\Psr7\Request;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestInterface;

interface HttpRequestBuilderInterface
{
    /**
     * @param \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestInterface $request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createGuzzleRequest(HttpRequestInterface $request): Request;
}
