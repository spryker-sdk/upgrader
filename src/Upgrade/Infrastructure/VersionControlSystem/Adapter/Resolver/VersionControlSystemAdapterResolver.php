<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver;

use Upgrade\Infrastructure\Exception\VersionControlSystemAdapterIsNotDefined;
use Upgrade\Infrastructure\VersionControlSystem\Adapter\VersionControlSystemAdapterInterface;

class VersionControlSystemAdapterResolver
{
    /**
     * @var array<\Upgrade\Infrastructure\VersionControlSystem\Adapter\VersionControlSystemAdapterInterface>
     */
    protected $adapters = [];

    /**
     * @param array<\Upgrade\Infrastructure\VersionControlSystem\Adapter\VersionControlSystemAdapterInterface> $adapters
     */
    public function __construct(array $adapters = [])
    {
        $this->adapters = $adapters;
    }

    /**
     * @param string $type
     *
     * @throws \Upgrade\Infrastructure\Exception\VersionControlSystemAdapterIsNotDefined
     *
     * @return \Upgrade\Infrastructure\VersionControlSystem\Adapter\VersionControlSystemAdapterInterface
     */
    public function resolve(string $type = 'git'): VersionControlSystemAdapterInterface
    {
        /** @var \Upgrade\Infrastructure\VersionControlSystem\Adapter\VersionControlSystemAdapterInterface $adapter */
        foreach ($this->adapters as $adapter) {
            if ($adapter->getType() === $type) {
                return $adapter;
            }
        }

        throw new VersionControlSystemAdapterIsNotDefined();
    }
}
