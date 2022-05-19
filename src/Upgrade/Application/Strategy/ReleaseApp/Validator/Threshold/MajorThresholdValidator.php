<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Exception\UpgraderException;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

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
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $releaseReleaseGroupDtoCollection
     *
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(ReleaseGroupDtoCollection $releaseReleaseGroupDtoCollection): void
    {
        $softThreshold = $this->configurationProvider->getSoftThresholdMajor();
        if ($releaseReleaseGroupDtoCollection->getCommonModuleCollection()->getMajorAmount() > $softThreshold) {
            throw new UpgraderException(
                sprintf('Soft threshold hit by %s major releases', $softThreshold),
            );
        }
    }
}