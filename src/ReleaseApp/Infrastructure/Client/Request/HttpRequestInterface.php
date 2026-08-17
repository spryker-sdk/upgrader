<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Client\Request;

use ReleaseApp\Domain\Client\Request\RequestInterface;

interface HttpRequestInterface
{
    /**
     * @var string
     */
    public const REQUEST_METHOD_POST = 'POST';

    /**
     * @var string
     */
    public const REQUEST_METHOD_GET = 'GET';

    public function getRequest(): RequestInterface;

    public function getEndpoint(): string;

    public function getMethod(): string;
}
