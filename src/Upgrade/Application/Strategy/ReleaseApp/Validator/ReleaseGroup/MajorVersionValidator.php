<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Exception\ReleaseGroupValidatorException;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\Common\Module\BetaMajorModule\BetaMajorModulesFetcherInterface;

class MajorVersionValidator implements ReleaseGroupValidatorInterface
{
    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var \Upgrade\Application\Strategy\Common\Module\BetaMajorModule\BetaMajorModulesFetcherInterface
     */
    protected BetaMajorModulesFetcherInterface $betaMajorModulesFetcher;

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \Upgrade\Application\Strategy\Common\Module\BetaMajorModule\BetaMajorModulesFetcherInterface $betaMajorModulesFetcher
     */
    public function __construct(ConfigurationProviderInterface $configurationProvider, BetaMajorModulesFetcherInterface $betaMajorModulesFetcher)
    {
        $this->configurationProvider = $configurationProvider;
        $this->betaMajorModulesFetcher = $betaMajorModulesFetcher;
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

        $moduleWithMajorUpdates = $releaseGroup->getModuleCollection()->getMajors();
        $moduleWithBetaMajorUpdates = $this->betaMajorModulesFetcher->getBetaMajorsNotInstalledInDev($releaseGroup->getModuleCollection());

        if (count($moduleWithMajorUpdates) === 0 && count($moduleWithBetaMajorUpdates) === 0) {
            return;
        }

        $message = sprintf(
            'There is a major release available for module %s. Please follow the link below to find all documentation needed to help you upgrade to the latest release %s',
            implode(', ', array_map(static fn (ModuleDto $module): string => $module->getName(), array_merge($moduleWithMajorUpdates, $moduleWithBetaMajorUpdates))),
            PHP_EOL . $releaseGroup->getLink(),
        );

        throw new ReleaseGroupValidatorException($message);
    }

    /**
     * @return string
     */
    public static function getValidatorTitle(): string
    {
        return 'Major Module Versions requiring manual action';
    }
}
