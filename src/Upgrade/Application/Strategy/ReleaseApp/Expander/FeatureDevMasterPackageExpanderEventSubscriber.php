<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Expander;

use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;

class FeatureDevMasterPackageExpanderEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected const DEV_MASTER_PREFIX = 'dev-master';

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @var bool
     */
    protected bool $isFeatureToDevMasterEnabled;

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     * @param bool $isFeatureToDevMasterEnabled
     */
    public function __construct(
        ConfigurationProviderInterface $configurationProvider,
        PackageManagerAdapterInterface $packageManager,
        bool $isFeatureToDevMasterEnabled = false
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->packageManager = $packageManager;
        $this->isFeatureToDevMasterEnabled = $isFeatureToDevMasterEnabled;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ReleaseGroupProcessorEvent::PRE_REQUIRE => 'onPreRequire',
        ];
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPreRequire(ReleaseGroupProcessorEvent $event): void
    {
        if (!$this->configurationProvider->getReleaseGroupId() || !$this->isFeatureToDevMasterEnabled) {
            return;
        }

        $currentReleaseGroup = $event->getStepsExecutionDto()->getCurrentReleaseGroup();
        if ($currentReleaseGroup === null) {
            return;
        }

        $featurePackages = $this->getFeaturePackagesForUpdate($currentReleaseGroup);

        foreach ($featurePackages as $featurePackage => $version) {
            $currentReleaseGroup->getFeaturePackages()->add(new ModuleDto($featurePackage, $version));
        }
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $currentReleaseGroup
     *
     * @return array<string, string>
     */
    protected function getFeaturePackagesForUpdate(ReleaseGroupDto $currentReleaseGroup): array
    {
        $featurePackages = $this->getFeaturePackages($currentReleaseGroup);
        $composerJson = $this->packageManager->getComposerJsonFile();
        $packages = array_merge($composerJson['require'], $composerJson['require-dev'] ?? []);
        $featurePackagesForUpdate = [];
        foreach ($featurePackages as $featurePackage => $version) {
            if (!isset($packages[$featurePackage]) || strpos($packages[$featurePackage], static::DEV_MASTER_PREFIX) !== false) {
                continue;
            }
            $featurePackagesForUpdate[$featurePackage] = $version;
        }

        return $featurePackagesForUpdate;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $currentReleaseGroup
     *
     * @return array<string, string>
     */
    protected function getFeaturePackages(ReleaseGroupDto $currentReleaseGroup): array
    {
        $featurePackages = [];

        foreach ($currentReleaseGroup->getModuleCollection()->toArray() as $moduleDto) {
            foreach ($moduleDto->getFeaturePackages() as $package => $version) {
                $featurePackages[$package] = $version;
            }
        }

        return $featurePackages;
    }
}
