<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;

class AbstractStep
{
    /**
     * @var \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface
     */
    protected VersionControlSystemAdapterInterface $vsc;

    /**
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $versionControlSystem
     */
    public function __construct(VersionControlSystemAdapterInterface $versionControlSystem)
    {
        $this->vsc = $versionControlSystem;
    }
}
