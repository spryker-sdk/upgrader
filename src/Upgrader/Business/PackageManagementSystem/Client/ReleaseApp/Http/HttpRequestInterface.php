<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http;

interface HttpRequestInterface
{
    /**
     * @var string
     */
    public const REQUEST_TYPE_POST = 'POST';

    /**
     * @var string
     */
    public const REQUEST_TYPE_GET = 'GET';

    /**
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return string|null
     */
    public function getBody(): ?string;

    /**
     * @return string
     */
    public function getResponseClass(): string;
}
