<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http;

interface HttpResponseInterface
{
    /**
     * @param int $code
     * @param string $body
     */
    public function __construct(int $code, string $body);

    /**
     * @return int
     */
    public function getCode(): int;

    /**
     * @return array|null
     */
    public function getBodyArray(): ?array;
}
