<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\VersionControlSystem\Client\VersionControlSystemClientInterface;

class VersionControlSystem implements VersionControlSystemInterface
{
    /**
     * @var \Upgrader\Business\VersionControlSystem\Client\VersionControlSystemClientInterface
     */
    protected $vcsClient;

    /**
     * @param \Upgrader\Business\VersionControlSystem\Client\VersionControlSystemClientInterface $vcsClient
     */
    public function __construct(VersionControlSystemClientInterface $vcsClient)
    {
        $this->vcsClient = $vcsClient;
    }

    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function checkUncommittedChanges(): CommandResultOutput
    {
        return $this->vcsClient->isUncommittedChangesExist();
    }
}
