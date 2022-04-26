<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Application\Client;

use ReleaseAppClient\Domain\Client\ReleaseAppClient as DomainReleaseAppClient;
use ReleaseAppClient\Domain\Client\ReleaseAppClientInterface;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto;

class ReleaseAppClient implements ReleaseAppClientInterface
{
    /**
     * @var \ReleaseAppClient\Domain\Client\ReleaseAppClient
     */
    protected DomainReleaseAppClient $domainReleaseAppClient;

    /**
     * @param \ReleaseAppClient\Domain\Client\ReleaseAppClient $domainReleaseAppClient
     */
    public function __construct(DomainReleaseAppClient $domainReleaseAppClient)
    {
        $this->domainReleaseAppClient = $domainReleaseAppClient;
    }

    /**
     * @param \ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto $request
     *
     * @return \ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto
     */
    public function getNotInstalledReleaseGroupList(ReleaseAppClientRequestDto $request): ReleaseAppClientResponseDto
    {
        return $this->domainReleaseAppClient->getNotInstalledReleaseGroupList($request);
    }
}
