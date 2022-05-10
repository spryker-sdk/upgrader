<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Repository\Builder;

use Psr\Http\Message\ResponseInterface;
use \ReleaseApp\Domain\Client\Response\ResponseInterface as DomainResponse;
use ReleaseApp\Infrastructure\Repository\Request\HttpRequestInterface;

interface HttpResponseBuilderInterface
{
    /**
     * @param HttpRequestInterface $request
     * @param ResponseInterface $guzzleResponse
     * @return DomainResponse
     */
    public function createHttpResponse(HttpRequestInterface $request, ResponseInterface $guzzleResponse): DomainResponse;
}
