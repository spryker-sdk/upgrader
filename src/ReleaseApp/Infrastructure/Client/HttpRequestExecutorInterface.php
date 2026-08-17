<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Client;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpRequestExecutorInterface
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(RequestInterface $request): ResponseInterface;
}
