<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http;

use ReleaseAppClient\Domain\Http\HttpRequestInterface;
use ReleaseAppClient\Domain\Http\HttpResponseInterface;

interface HttpClientInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Http\HttpRequestInterface $request
     *
     * @return \ReleaseAppClient\Domain\Http\HttpResponseInterface
     */
    public function send(HttpRequestInterface $request): HttpResponseInterface;
}
