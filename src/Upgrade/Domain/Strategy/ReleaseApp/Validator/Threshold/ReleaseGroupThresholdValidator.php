<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\UpgraderException;

class ReleaseGroupThresholdValidator implements ThresholdValidatorInterface
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection $releaseGroupDtoCollection
     *
     * @return void
     *@throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     */
    public function validate(ReleaseGroupDtoCollection $releaseGroupDtoCollection): void
    {
        if (
            $releaseGroupDtoCollection->count() >= $this->configurationProvider->getThresholdReleaseGroup()
            &&
            $this->configurationProvider->getReleaseGroupRequireProcessor() == ConfigurationProvider::SEQUENTIAL_RELEASE_GROUP_REQUIRE_PROCESSOR
        ) {
            throw new UpgraderException('Release group amount limit reached');
        }
    }
}
