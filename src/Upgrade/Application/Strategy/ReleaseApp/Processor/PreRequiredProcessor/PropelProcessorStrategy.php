<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;

class PropelProcessorStrategy implements PreRequireProcessorStrategyInterface
{
    /**
     * @var string
     */
    public const PACKAGE_NAME = 'propel/propel';

    /**
     * @var string
     */
    public const LOCK_PACKAGE_VERSION = '2.0.0-beta2';

    /**
     * @var string
     */
    protected const RELEASE_GROUP__NAME = 'updater-rg';

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(PackageManagerAdapterInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $requireCollection
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection
     */
    public function process(ReleaseGroupDtoCollection $requireCollection): ReleaseGroupDtoCollection
    {
        $packageVersion = $this->packageManager->getPackageVersion(static::PACKAGE_NAME);

        if ($packageVersion !== static::LOCK_PACKAGE_VERSION) {
            return $requireCollection;
        }

        if ($this->alreadyHasRequiredPropelPackage()) {
            return $requireCollection;
        }

        $requireCollection->add(
            new ReleaseGroupDto(
                static::RELEASE_GROUP__NAME,
                new ModuleDtoCollection(
                    [new ModuleDto(static::PACKAGE_NAME, static::LOCK_PACKAGE_VERSION, 'minor')],
                ),
                false,
                '',
            ),
        );

        return $requireCollection;
    }

    /**
     * @return bool
     */
    protected function alreadyHasRequiredPropelPackage(): bool
    {
        $composerJson = $this->packageManager->getComposerJsonFile();

        $packages = array_merge($composerJson['require'], $composerJson['require-dev'] ?? []);

        return array_key_exists(static::PACKAGE_NAME, $packages);
    }
}
