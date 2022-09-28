<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Adapter;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;

interface ReleaseAppClientAdapterInterface
{
    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getNewReleaseGroups(): ReleaseAppResponse;
}
