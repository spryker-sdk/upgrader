<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Exception\ReleaseGroupThresholdException;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class PathThresholdValidator implements ThresholdValidatorInterface
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
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $releaseGroupDtoCollection
     *
     * @throws \Upgrade\Application\Exception\ReleaseGroupThresholdException
     *
     * @return void
     */
    public function validate(ReleaseGroupDtoCollection $releaseGroupDtoCollection): void
    {
        $softThreshold = $this->configurationProvider->getSoftThresholdBugfix();
        if ($releaseGroupDtoCollection->getCommonModuleCollection()->getPathAmount() > $softThreshold) {
            throw new ReleaseGroupThresholdException(
                sprintf('Soft threshold hit by %s major releases', $softThreshold),
            );
        }
    }
}
