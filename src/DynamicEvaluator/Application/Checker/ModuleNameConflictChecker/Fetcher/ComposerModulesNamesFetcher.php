<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher;

use Core\Infrastructure\StringHelper;
use Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReader;

class ComposerModulesNamesFetcher implements ComposerModulesNamesFetcherInterface
{
    /**
     * @var string
     */
    protected const SPRYKER_REPOSITORY_PREFIX = 'spryker';

    /**
     * @var \Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReader
     */
    protected ComposerLockReader $composerLockReader;

    /**
     * @param \Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReader $composerLockReader
     */
    public function __construct(ComposerLockReader $composerLockReader)
    {
        $this->composerLockReader = $composerLockReader;
    }

    /**
     * @return array<string>
     */
    public function fetchComposerModules(): array
    {
        $composerLockMap = $this->composerLockReader->read();

        $sprykerModules = [];

        foreach (['packages', 'packages-dev'] as $key) {
            $packages = array_map(static fn (array $composerPackageData): string => $composerPackageData['name'], $composerLockMap[$key]);
            $sprykerPackages = array_filter($packages, static fn (string $package): bool => strpos($package, static::SPRYKER_REPOSITORY_PREFIX) === 0);

            $sprykerModules[] = array_map([$this, 'getModuleFromPackage'], $sprykerPackages);
        }

        return array_unique(array_merge(...$sprykerModules));
    }

    /**
     * @param string $package
     *
     * @return string
     */
    protected function getModuleFromPackage(string $package): string
    {
        [, $moduleName] = explode('/', $package);

        return StringHelper::fromDashToCamelCase($moduleName);
    }
}
