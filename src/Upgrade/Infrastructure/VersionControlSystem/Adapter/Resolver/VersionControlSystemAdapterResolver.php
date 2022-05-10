<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver;

use Upgrade\Infrastructure\Exception\VersionControlSystemAdapterIsNotDefined;
use Upgrade\Application\Provider\VersionControlSystemProviderInterface;

class VersionControlSystemAdapterResolver
{
    /**
     * @var array<\Upgrade\Application\Provider\VersionControlSystemProviderInterface>
     */
    protected $adapters = [];

    /**
     * @param array<\Upgrade\Application\Provider\VersionControlSystemProviderInterface> $adapters
     */
    public function __construct(array $adapters = [])
    {
        $this->adapters = $adapters;
    }

    /**
     * @param string $type
     *
     * @return \Upgrade\Application\Provider\VersionControlSystemProviderInterface
     *@throws \Upgrade\Infrastructure\Exception\VersionControlSystemAdapterIsNotDefined
     *
     */
    public function resolve(string $type = 'git'): VersionControlSystemProviderInterface
    {
        /** @var \Upgrade\Application\Provider\VersionControlSystemProviderInterface $adapter */
        foreach ($this->adapters as $adapter) {
            if ($adapter->getType() === $type) {
                return $adapter;
            }
        }

        throw new VersionControlSystemAdapterIsNotDefined();
    }
}
