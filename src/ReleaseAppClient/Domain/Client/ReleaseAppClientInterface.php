<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Client;

use ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto;

interface ReleaseAppClientInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto $request
     *
     * @return \ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto
     */
    public function getNotInstalledReleaseGroupList(ReleaseAppClientRequestDto $request): ReleaseAppClientResponseDto;
}
