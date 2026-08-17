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

class ReleaseGroupThresholdValidator implements ThresholdValidatorInterface
{
    protected ConfigurationProvider $configurationProvider;

    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @throws \Upgrade\Application\Exception\ReleaseGroupThresholdException
     */
    public function validate(ReleaseGroupDtoCollection $releaseGroupDtoCollection): void
    {
        if (
            $releaseGroupDtoCollection->count() >= $this->configurationProvider->getThresholdReleaseGroup()
            &&
            $this->configurationProvider->getReleaseGroupProcessor() == ConfigurationProvider::SEQUENTIAL_RELEASE_GROUP_PROCESSOR
        ) {
            throw new ReleaseGroupThresholdException('Release group amount limit reached');
        }
    }
}
