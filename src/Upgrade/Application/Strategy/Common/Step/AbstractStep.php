<?php


/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver;

class AbstractStep
{
    /**
     * @var \Upgrade\Application\Provider\VersionControlSystemProviderInterface
     */
    protected $vsc;

    /**
     * @param \Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver\VersionControlSystemAdapterResolver $vscAdapterResolver
     */
    public function __construct(VersionControlSystemAdapterResolver $vscAdapterResolver)
    {
        $this->vsc = $vscAdapterResolver->resolve();
    }
}
