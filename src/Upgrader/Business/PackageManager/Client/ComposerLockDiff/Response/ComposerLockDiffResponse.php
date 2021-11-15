<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\ComposerLockDiff\Response;

use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;
use Upgrader\Business\PackageManager\Transfer\PackageTransfer;

class ComposerLockDiffResponse
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
     * @return \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection
     */
    public function getUpdatedPackages(): PackageTransferCollection
    {
        $response = new PackageTransferCollection();

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
     * @return \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection
     */
    public function getChanges(): PackageTransferCollection
    {
        $packages = [];

        foreach ($this->bodyArray[static::CHANGES_KEY] as $packageName => $packageData) {
            $packages[] = new PackageTransfer($packageName, $packageData[1]);
        }

        return new PackageTransferCollection($packages);
    }

    /**
     * @return \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection
     */
    public function getChangesDev(): PackageTransferCollection
    {
        $packages = [];

        foreach ($this->bodyArray[static::CHANGES_DEV_KEY] as $packageName => $packageData) {
            $packages[] = new PackageTransfer($packageName, $packageData[1]);
        }

        return new PackageTransferCollection($packages);
    }
}
