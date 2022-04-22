<?php

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\Threshold;

use Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\UpgraderException;

class MinorThresholdValidator implements ThresholdValidatorInterface
{
    /**
     * @var ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param array $processorList
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param ReleaseGroupDtoCollection $releaseGroupDtoCollection
     * @return void
     * @throws UpgraderException
     */
    public function validate(ReleaseGroupDtoCollection $releaseGroupDtoCollection): void
    {
        $minorAmount = $releaseGroupDtoCollection->getCommonModuleCollection()->getMinorAmount();
        if ($minorAmount > $this->configurationProvider->getSoftThresholdMinorAmount()) {
            throw new UpgraderException('Soft threshold hit by minor releases amount');
        }
    }
}
