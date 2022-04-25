<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Dto\Composer;

class ComposerLockDiffDto
{
    /**
     * @var string
     */
    protected const CHANGES_KEY = 'changes';

    /**
     * @var string
     */
    protected const CHANGES_DEV_KEY = 'changes-dev';

    /**
     * @var array
     */
    protected $composerLockDiff = [];

    /**
     * @param array $composerLockDiff
     */
    public function __construct(array $composerLockDiff = [])
    {
        $this->composerLockDiff = $composerLockDiff;
    }

    /**
     * @return array<\Upgrade\Domain\Dto\Composer\PackageDto>
     */
    public function getRequireChanges(): array
    {
        return $this->getChangesByKey(static::CHANGES_KEY);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->getRequireChanges() + $this->getRequireDevChanges()) === 0;
    }

    /**
     * @return array<\Upgrade\Domain\Dto\Composer\PackageDto>
     */
    public function getRequireDevChanges(): array
    {
        return $this->getChangesByKey(static::CHANGES_DEV_KEY);
    }

    /**
     * @param string $key
     *
     * @return array<\Upgrade\Domain\Dto\Composer\PackageDto>
     */
    protected function getChangesByKey(string $key): array
    {
        $packages = [];

        if (!isset($this->composerLockDiff[$key])) {
            return $packages;
        }

        foreach ($this->composerLockDiff[$key] as $packageName => $packageData) {
            $version = $packageData[1] ?? '';
            $previousVersion = $packageData[0] ?? '';
            $diffLink = $packageData[2] ?? '';

            $packages[] = new PackageDto($packageName, $version, $previousVersion, $diffLink);
        }

        return $packages;
    }
}
