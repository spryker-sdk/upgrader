<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Bridge\VersionControlSystemBridgeInterface;

class AbstractStep
{
    /**
     * @var \Upgrade\Application\Bridge\VersionControlSystemBridgeInterface
     */
    protected VersionControlSystemBridgeInterface $vsc;

    /**
     * @param \Upgrade\Application\Bridge\VersionControlSystemBridgeInterface $versionControlSystem
     */
    public function __construct(VersionControlSystemBridgeInterface $versionControlSystem)
    {
        $this->vsc = $versionControlSystem;
    }
}
