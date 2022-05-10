<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Application\Exception\ReleaseGroupRequireProcessorIsNotDefinedException;

class ReleaseGroupRequireProcessorResolver
{
    /**
     * @var array<\Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface>
     */
    protected $processorList = [];

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param array<\Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface> $processorList
     */
    public function __construct(ConfigurationProvider $configurationProvider, array $processorList)
    {
        $this->configurationProvider = $configurationProvider;
        $this->processorList = $processorList;
    }

    /**
     * @return \Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface
     *@throws \Upgrade\Application\Exception\ReleaseGroupRequireProcessorIsNotDefinedException
     *
     */
    public function getProcessor(): ReleaseGroupRequireProcessorInterface
    {
        $processorName = $this->configurationProvider->getReleaseGroupRequireProcessor();
        foreach ($this->processorList as $processor) {
            if ($processor->getProcessorName() === $processorName) {
                return $processor;
            }
        }

        throw new ReleaseGroupRequireProcessorIsNotDefinedException();
    }
}
