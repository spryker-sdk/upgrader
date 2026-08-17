<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Client\Builder;

use Psr\Http\Message\ResponseInterface;
use ReleaseApp\Domain\Client\Response\ResponseInterface as DomainResponse;
use ReleaseApp\Infrastructure\Client\Request\HttpRequestInterface;

interface HttpResponseBuilderInterface
{
    public function createHttpResponse(HttpRequestInterface $request, ResponseInterface $guzzleResponse): DomainResponse;
}
