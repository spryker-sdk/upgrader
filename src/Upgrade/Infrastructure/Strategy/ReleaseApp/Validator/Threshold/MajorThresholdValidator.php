<?php

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\Threshold;

use Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection;
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
        $majorAmount = $releaseReleaseGroupDtoCollection->getCommonModuleCollection()->getMajorAmount();
        if ($majorAmount > $this->configurationProvider->getSoftThresholdMajorAmount()) {
            throw new UpgraderException('Soft threshold hit by major releases amount');
        }
    }
}
