<?php

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Processor;

use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\ReleaseGroupRequireProcessorIsNotDefinedException;

class ReleaseGroupRequireProcessorResolver
{
    /**
     * @var array<ReleaseGroupRequireProcessorInterface>
     */
    protected $processorList = [];

    /**
     * @var ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param array $processorList
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider, array $processorList)
    {
        $this->configurationProvider = $configurationProvider;
        $this->processorList = $processorList;
    }

    /**
     * @param string $strategyName
     *
     * @return \Upgrade\Infrastructure\Strategy\StrategyInterface
     * @throws \Upgrade\Infrastructure\Exception\UpgradeStrategyIsNotDefinedException
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
