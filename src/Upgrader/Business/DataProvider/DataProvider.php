<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
