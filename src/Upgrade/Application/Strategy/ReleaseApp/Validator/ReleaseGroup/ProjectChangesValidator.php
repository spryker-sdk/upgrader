<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Exception\ReleaseGroupValidatorException;
use Upgrade\Application\Provider\ConfigurationProviderInterface;

class ProjectChangesValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(ConfigurationProviderInterface $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     *
     * @throws \Upgrade\Application\Exception\ReleaseGroupValidatorException
     *
     * @return void
     */
    public function validate(ReleaseGroupDto $releaseGroup): void
    {
        if ($this->configurationProvider->getSoftThresholdMajor()) {
            return;
        }

        if ($releaseGroup->hasProjectChanges()) {
            $message = sprintf(
                'Release group "%s" contains changes on project level. Please follow the link below to find all documentation needed to help you upgrade to the latest release %s',
                $releaseGroup->getName(),
                PHP_EOL . $releaseGroup->getLink(),
            );

            throw new ReleaseGroupValidatorException($message);
        }
    }
}
