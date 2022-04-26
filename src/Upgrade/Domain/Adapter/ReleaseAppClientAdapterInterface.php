<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Adapter;

use ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto;

interface ReleaseAppClientAdapterInterface
{
    /**
     * @return \ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto
     */
    public function getNotInstalledReleaseGroupList(): ReleaseAppClientResponseDto;
}
