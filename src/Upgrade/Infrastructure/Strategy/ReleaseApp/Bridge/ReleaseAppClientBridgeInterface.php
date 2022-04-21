<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Bridge;

use Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientResponseDto;

interface ReleaseAppClientBridgeInterface
{
    /**
     * @return \Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientResponseDto
     */
    public function getNotInstalledReleaseGroupList(): ReleaseAppClientResponseDto;
}
