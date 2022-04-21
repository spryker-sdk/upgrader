<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http;

interface HttpResponseInterface
{
    /**
     * @return int
     */
    public function getCode(): int;

    /**
     * @return array|null
     */
    public function getBody(): ?array;
}
