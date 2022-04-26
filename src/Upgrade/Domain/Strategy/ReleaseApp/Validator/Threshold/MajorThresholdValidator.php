<?php

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\UpgraderException;

class MajorThresholdValidator implements ThresholdValidatorInterface
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
     * @param ReleaseGroupDtoCollection $releaseReleaseGroupDtoCollection
     * @return void
     * @throws UpgraderException
     */
    public function validate(ReleaseGroupDtoCollection $releaseReleaseGroupDtoCollection): void
    {
        $softThreshold = $this->configurationProvider->getSoftThresholdMajorAmount();
        if ($releaseReleaseGroupDtoCollection->getCommonModuleCollection()->getMajorAmount() > $softThreshold) {
            throw new UpgraderException(
                sprintf('Soft threshold hit by %s major releases amount', $softThreshold)
            );
        }
    }
}
