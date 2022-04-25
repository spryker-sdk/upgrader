<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Bridge;

use ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto;

interface ReleaseAppClientBridgeInterface
{
    /**
     * @return \ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto
     */
    public function getNotInstalledReleaseGroupList(): ReleaseAppClientResponseDto;
}
