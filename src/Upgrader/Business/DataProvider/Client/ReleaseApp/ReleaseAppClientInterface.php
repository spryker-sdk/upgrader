<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp;

use Upgrader\Business\DataProvider\Request\DataProviderRequestInterface;
use Upgrader\Business\DataProvider\Response\DataProviderResponse;

interface ReleaseAppClientInterface
{
    /**
     * @param \Upgrader\Business\DataProvider\Request\DataProviderRequestInterface $request
     *
     * @return \Upgrader\Business\DataProvider\Response\DataProviderResponse
     */
    public function getNotInstalledReleaseGroupList(DataProviderRequestInterface $request): DataProviderResponse;
}
