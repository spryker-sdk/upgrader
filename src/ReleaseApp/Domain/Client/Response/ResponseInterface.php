<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Client\Response;

interface ResponseInterface
{
    /**
     * @return int
     */
    public function getCode(): int;

    /**
     * @return array<mixed>|null
     */
    public function getBody(): ?array;
}
