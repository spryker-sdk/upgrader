<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http;

interface HttpClientInterface
{
    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestInterface $request
     *
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpResponseInterface
     */
    public function send(HttpRequestInterface $request): HttpResponseInterface;
}
