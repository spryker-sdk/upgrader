<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request;

abstract class AbstractHttpRequest implements HttpRequestInterface
{
    public const REQUEST_TYPE_POST = 'POST';
    public const REQUEST_TYPE_GET = 'GET';
}
