<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client;

use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;

interface PackageManagerClientInterface
{
    /**
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function runUpdate(): CommandResponse;

    /**
     * @return string
     */
    public function getProjectName(): string;

    /**
     * @return array
     */
    public function getComposerJsonFile(): array;

    /**
     * @return array
     */
    public function getComposerLockFile(): array;

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function require(PackageTransferCollection $packageCollection): CommandResponse;

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string;
}
