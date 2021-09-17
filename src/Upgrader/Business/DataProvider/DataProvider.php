<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider;

use Upgrader\Business\DataProvider\Client\ReleaseApp\ReleaseAppClientInterface;
use Upgrader\Business\DataProvider\Request\DataProviderRequestInterface;
use Upgrader\Business\DataProvider\Response\DataProviderResponse;

class DataProvider implements DataProviderInterface
{
    /**
     * @var \Upgrader\Business\DataProvider\Client\ReleaseApp\ReleaseAppClientInterface
     */
    protected $releaseAppClient;

    /**
     * @param \Upgrader\Business\DataProvider\Client\ReleaseApp\ReleaseAppClientInterface $releaseAppClient
     */
    public function __construct(ReleaseAppClientInterface $releaseAppClient)
    {
        $this->releaseAppClient = $releaseAppClient;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Request\DataProviderRequestInterface $request
     *
     * @return \Upgrader\Business\DataProvider\Response\DataProviderResponse
     */
    public function getNotInstalledReleaseGroupList(DataProviderRequestInterface $request): DataProviderResponse
    {
        return $this->releaseAppClient->getNotInstalledReleaseGroupList($request);
    }
}
