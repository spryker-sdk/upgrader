<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository\Builder;

use GuzzleHttp\Psr7\Request;
use ReleaseApp\Domain\Entities\RequestInterface;

interface HttpRequestBuilderInterface
{
    /**
     * @param \ReleaseApp\Domain\Entities\RequestInterface $request
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createGuzzleRequest(RequestInterface $request): Request;
}
