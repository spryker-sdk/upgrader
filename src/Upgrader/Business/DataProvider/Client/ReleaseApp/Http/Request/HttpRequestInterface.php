<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request;

interface HttpRequestInterface
{
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
