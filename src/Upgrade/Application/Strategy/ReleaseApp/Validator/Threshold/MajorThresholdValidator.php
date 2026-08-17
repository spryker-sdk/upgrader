<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\Threshold;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Exception\ReleaseGroupThresholdException;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class MajorThresholdValidator implements ThresholdValidatorInterface
{
    protected ConfigurationProvider $configurationProvider;

    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @throws \Upgrade\Application\Exception\ReleaseGroupThresholdException
     */
    public function validate(ReleaseGroupDtoCollection $releaseReleaseGroupDtoCollection): void
    {
        $softThreshold = $this->configurationProvider->getSoftThresholdMajor();
        $majorsCount = count($releaseReleaseGroupDtoCollection->getCommonModuleCollection()->getMajors());

        if ($majorsCount && $majorsCount >= $softThreshold) {
            throw new ReleaseGroupThresholdException(
                sprintf('Soft threshold hit by %s major releases', $softThreshold),
            );
        }
    }
}
