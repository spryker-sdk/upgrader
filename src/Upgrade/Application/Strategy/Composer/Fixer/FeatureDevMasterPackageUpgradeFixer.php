<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Composer\Fixer;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class FeatureDevMasterPackageUpgradeFixer extends AbstractFeaturePackageUpgradeFixer
{
    /**
     * @var string
     */
    public const MASK_ALIAS_DEV_MASTER = 'dev-master as ^%s';

    /**
     * @var bool
     */
    protected bool $isFeatureToDevMasterEnabled;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     * @param bool $isFeatureToDevMasterEnabled
     */
    public function __construct(PackageManagerAdapterInterface $packageManager, bool $isFeatureToDevMasterEnabled = false)
    {
        $this->isFeatureToDevMasterEnabled = $isFeatureToDevMasterEnabled;

        parent::__construct($packageManager);
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     * @param \Upgrade\Application\Dto\PackageManagerResponseDto $packageManagerResponseDto
     *
     * @return bool
     */
    public function isApplicable(ReleaseGroupDto $releaseGroup, PackageManagerResponseDto $packageManagerResponseDto): bool
    {
        return $this->isFeatureToDevMasterEnabled && parent::isApplicable($releaseGroup, $packageManagerResponseDto);
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     * @param \Upgrade\Application\Dto\PackageManagerResponseDto $packageManagerResponseDto
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto|null
     */
    public function run(ReleaseGroupDto $releaseGroup, PackageManagerResponseDto $packageManagerResponseDto): ?PackageManagerResponseDto
    {
        preg_match_all(static::FEATURE_PACKAGE_PATTERN, (string)$packageManagerResponseDto->getOutputMessage(), $matches);

        if (!isset($matches[static::KEY_FEATURES]) || !$matches[static::KEY_FEATURES] || !is_array($matches[static::KEY_FEATURES])) {
            return null;
        }

        $version = sprintf(
            static::MASK_ALIAS_DEV_MASTER,
            date('Y', strtotime(date('m') <= 11 ? 'now' : '+1 year')) . '00.0',
        );

        $packageCollection = new PackageCollection(array_map(
            fn (string $featurePackage): Package => new Package($featurePackage, $version),
            $matches[static::KEY_FEATURES],
        ));

        return $this->packageManager->require($packageCollection);
    }
}
