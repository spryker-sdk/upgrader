<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\PackageManager;

use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;

class ComposerLockDiffResponseDto
{
    /**
     * @var string
     */
    public const CHANGES_KEY = 'changes';

    /**
     * @var string
     */
    public const CHANGES_DEV_KEY = 'changes-dev';

    /**
     * @var array
     */
    protected $bodyArray;

    /**
     * @param array $bodyArray
     */
    public function __construct(array $bodyArray)
    {
        $this->bodyArray = $bodyArray;
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function getUpdatedPackages(): PackageDtoCollection
    {
        $response = new PackageDtoCollection();

        $response->addCollection($this->getChanges());
        $response->addCollection($this->getChangesDev());

        return $response;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !($this->getChanges()->count() + $this->getChangesDev()->count());
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function getChanges(): PackageDtoCollection
    {
        $packages = [];

        foreach ($this->bodyArray[static::CHANGES_KEY] as $packageName => $packageData) {
            $packages[] = new PackageDto($packageName, $packageData[1]);
        }

        return new PackageDtoCollection($packages);
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function getChangesDev(): PackageDtoCollection
    {
        $packages = [];

        foreach ($this->bodyArray[static::CHANGES_DEV_KEY] as $packageName => $packageData) {
            $packages[] = new PackageDto($packageName, $packageData[1]);
        }

        return new PackageDtoCollection($packages);
    }
}
