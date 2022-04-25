<?php

namespace ReleaseAppClient\Application\Client;

use ReleaseAppClient\Domain\Client\ReleaseAppClientInterface;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto;
use ReleaseAppClient\Domain\Client\ReleaseAppClient as DomainReleaseAppClient;

class ReleaseAppClient implements ReleaseAppClientInterface
{
    /**
     * @var DomainReleaseAppClient
     */
    protected DomainReleaseAppClient $domainReleaseAppClient;

    /**
     * @param DomainReleaseAppClient $domainReleaseAppClient
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
