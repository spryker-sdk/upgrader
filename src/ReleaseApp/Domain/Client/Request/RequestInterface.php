<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Domain\Client\Request;

interface RequestInterface
{
    public function getBody(): ?string;

    public function getResponseClass(): string;

    public function getParameters(): ?string;
}
