<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Adapter;

use ReleaseApp\Infrastructure\Presentation\Entity\ReleaseAppResponse;

interface ReleaseAppClientAdapterInterface
{
    /**
     * @return \ReleaseApp\Infrastructure\Presentation\Entity\ReleaseAppResponse
     */
    public function getNotInstalledReleaseGroupList(): ReleaseAppResponse;
}
