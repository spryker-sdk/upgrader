<?php

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\UpgraderException;

class ReleaseGroupThresholdValidator implements ThresholdValidatorInterface
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
        if (
            $releaseGroupDtoCollection->count() >= $this->configurationProvider->getThresholdReleaseGroupAmount()
            &&
            $this->configurationProvider->getReleaseGroupRequireProcessor() == ConfigurationProvider::SEQUENTIAL_RELEASE_GROUP_REQUIRE_PROCESSOR
        ) {
            throw new UpgraderException('Release group amount limit reached');
        }
    }
}
