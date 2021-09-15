<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http;

use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\HttpRequestInterface;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponseInterface;

interface HttpCommunicatorInterface
{
    /**
     * @param \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\HttpRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(HttpRequestInterface $request): HttpResponseInterface;
}
