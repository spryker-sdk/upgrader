<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

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

    /**
     * @return \ReleaseApp\Domain\Client\Request\RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * @return string
     */
    public function getMethod(): string;
}
