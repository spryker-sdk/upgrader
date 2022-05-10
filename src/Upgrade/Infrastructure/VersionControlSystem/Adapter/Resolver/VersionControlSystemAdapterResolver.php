<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Adapter\Resolver;

use Upgrade\Infrastructure\Exception\VersionControlSystemAdapterIsNotDefined;
use Upgrade\Application\Bridge\VersionControlSystemBridgeInterface;

class VersionControlSystemAdapterResolver
{
    /**
     * @var array<\Upgrade\Application\Bridge\VersionControlSystemBridgeInterface>
     */
    protected $adapters = [];

    /**
     * @param array<\Upgrade\Application\Bridge\VersionControlSystemBridgeInterface> $adapters
     */
    public function __construct(array $adapters = [])
    {
        $this->adapters = $adapters;
    }

    /**
     * @param string $type
     *
     * @return \Upgrade\Application\Bridge\VersionControlSystemBridgeInterface
     *@throws \Upgrade\Infrastructure\Exception\VersionControlSystemAdapterIsNotDefined
     *
     */
    public function resolve(string $type = 'git'): VersionControlSystemBridgeInterface
    {
        /** @var \Upgrade\Application\Bridge\VersionControlSystemBridgeInterface $adapter */
        foreach ($this->adapters as $adapter) {
            if ($adapter->getType() === $type) {
                return $adapter;
            }
        }

        throw new VersionControlSystemAdapterIsNotDefined();
    }
}
