<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http;

use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\HttpRequestInterface;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponseInterface;

interface HttpClientInterface
{
    /**
     * @param \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\HttpRequestInterface $request
     *
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponseInterface
     */
    public function send(HttpRequestInterface $request): HttpResponseInterface;
}
