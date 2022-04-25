<?php

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
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
