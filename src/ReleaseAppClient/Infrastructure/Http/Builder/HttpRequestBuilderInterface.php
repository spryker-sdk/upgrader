<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Infrastructure\Http\Builder;

use GuzzleHttp\Psr7\Request;
use ReleaseAppClient\Domain\Http\HttpRequestInterface;

interface HttpRequestBuilderInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Http\HttpRequestInterface $request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createGuzzleRequest(HttpRequestInterface $request): Request;
}
