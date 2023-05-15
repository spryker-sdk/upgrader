<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer;

interface PackagesDirProviderInterface
{
    /**
     * @return array<string>
     */
    public function getSprykerPackageDirs(): array;

    /**
     * @return string
     */
    public function getFromDir(): string;

    /**
     * @return string
     */
    public function getToDir(): string;
}
