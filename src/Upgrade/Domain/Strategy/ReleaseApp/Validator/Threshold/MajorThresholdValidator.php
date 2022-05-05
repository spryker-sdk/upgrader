<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\UpgraderException;

class MajorThresholdValidator implements ThresholdValidatorInterface
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
     * @param \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection $releaseReleaseGroupDtoCollection
     *
     * @return void
     *@throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     */
    public function validate(ReleaseGroupDtoCollection $releaseReleaseGroupDtoCollection): void
    {
        $softThreshold = $this->configurationProvider->getSoftThresholdMajor();
        if ($releaseReleaseGroupDtoCollection->getCommonModuleCollection()->getMajorAmount() > $softThreshold) {
            throw new UpgraderException(
                sprintf('Soft threshold hit by %s major releases amount', $softThreshold),
            );
        }
    }
}
