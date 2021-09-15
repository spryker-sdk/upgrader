<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request;

abstract class AbstractHttpRequest implements HttpRequestInterface
{
    public const REQUEST_TYPE_POST = 'POST';
    public const REQUEST_TYPE_GET = 'GET';
}
