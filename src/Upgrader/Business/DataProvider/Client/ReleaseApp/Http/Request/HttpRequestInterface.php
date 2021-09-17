<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
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
