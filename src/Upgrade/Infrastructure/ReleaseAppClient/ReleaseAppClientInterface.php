<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient;

use Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientRequestDto;
use Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientResponseDto;

interface ReleaseAppClientInterface
{
    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientRequestDto $request
     *
     * @return \Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientResponseDto
     */
    public function getNotInstalledReleaseGroupList(ReleaseAppClientRequestDto $request): ReleaseAppClientResponseDto;
}
