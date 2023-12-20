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

class FeaturePackageUpgradeFixer extends AbstractFeaturePackageUpgradeFixer
{
    /**
     * @var bool
     */
    protected bool $isReleaseGroupIntegratorEnabled;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     * @param bool $isReleaseGroupIntegratorEnabled
     */
    public function __construct(PackageManagerAdapterInterface $packageManager, bool $isReleaseGroupIntegratorEnabled = false)
    {
        $this->isReleaseGroupIntegratorEnabled = $isReleaseGroupIntegratorEnabled;

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
        if ($this->isReleaseGroupIntegratorEnabled) {
            return false;
        }

        return parent::isApplicable($releaseGroup, $packageManagerResponseDto);
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     * @param \Upgrade\Application\Dto\PackageManagerResponseDto $packageManagerResponseDto
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto|null
     */
    public function run(ReleaseGroupDto $releaseGroup, PackageManagerResponseDto $packageManagerResponseDto): ?PackageManagerResponseDto
    {
        $messages = $packageManagerResponseDto->getOutputMessage();
        preg_match_all(static::FEATURE_PACKAGE_PATTERN, (string)$packageManagerResponseDto->getOutputMessage(), $matches);

        if (empty($matches[static::KEY_FEATURES]) || !is_array($matches[static::KEY_FEATURES])) {
            return null;
        }

        $featurePackages = $this->getPackagesFromFeatures($matches[static::KEY_FEATURES]);

        $packageCollection = new PackageCollection($featurePackages);

        $response = $this->packageManager->require($packageCollection);

        if (!$response->isSuccessful()) {
            return $response;
        }

        $responseDto = $this->packageManager->remove(new PackageCollection(array_map(
            fn (string $featurePackage): Package => new Package($featurePackage),
            $matches[static::KEY_FEATURES],
        )));

        return $responseDto;
    }

    /**
     * @param array<string> $featurePackages
     *
     * @return array<\Upgrade\Domain\Entity\Package>
     */
    protected function getPackagesFromFeatures(array $featurePackages): array
    {
        $composerLockFile = $this->packageManager->getComposerLockFile();
        $packages = [];
        if (!isset($composerLockFile['packages'])) {
            return [];
        }

        foreach ($composerLockFile['packages'] as $lockPackage) {
            foreach ($featurePackages as $featurePackage) {
                if ($lockPackage['name'] !== $featurePackage) {
                    continue;
                }

                unset($lockPackage['require']['php']);
                $packages[] = $lockPackage['require'];
            }
        }
        $packages = array_merge(...$packages);

        return array_map(
            fn (string $featurePackage, string $version): Package => new Package($featurePackage, $version),
            array_keys($packages),
            array_values($packages),
        );
    }
}
