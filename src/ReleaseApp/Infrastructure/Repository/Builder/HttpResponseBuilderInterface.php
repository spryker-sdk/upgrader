<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository\Builder;

use Psr\Http\Message\ResponseInterface;
use ReleaseApp\Domain\Entities\RequestInterface;
use ReleaseApp\Domain\Entities\ResponseInterface;

interface HttpResponseBuilderInterface
{
    /**
     * @param \ReleaseApp\Domain\Entities\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $guzzleResponse
     *
     * @return \ReleaseApp\Domain\Entities\ResponseInterface
     */
    public function createHttpResponse(RequestInterface $request, ResponseInterface $guzzleResponse): ResponseInterface;
}
