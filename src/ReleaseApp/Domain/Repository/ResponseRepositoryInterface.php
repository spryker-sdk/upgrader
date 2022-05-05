<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Repository;

use ReleaseApp\Domain\Entities\RequestInterface;
use ReleaseApp\Domain\Entities\ResponseInterface;

interface ResponseRepositoryInterface
{
    /**
     * @param \ReleaseApp\Domain\Entities\RequestInterface $request
     *
     * @return \ReleaseApp\Domain\Entities\ResponseInterface
     */
    public function getResponse(RequestInterface $request): ResponseInterface;
}
