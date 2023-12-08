<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule;

use Upgrade\Infrastructure\PackageManager\Reader\ComposerReaderInterface;

class SprykerModulesStateFetcher implements SprykerModulesStateFetcherInterface
{
    /**
     * @var string
     */
    protected const PACKAGES_KEY = 'packages';

    /**
     * @var string
     */
    protected const PACKAGES_DEV_KEY = 'packages-dev';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var string
     */
    protected const VERSION_KEY = 'version';

    /**
     * @var string
     */
    protected const SPRYKER_PREFIX = 'spryker';

    /**
     * @var string
     */
    protected const EXCLUDED_VENDOR = 'spryker-feature';

    /**
     * @var \Upgrade\Infrastructure\PackageManager\Reader\ComposerReaderInterface
     */
    protected ComposerReaderInterface $composerLockReader;

    /**
     * @param \Upgrade\Infrastructure\PackageManager\Reader\ComposerReaderInterface $composerLockReader
     */
    public function __construct(ComposerReaderInterface $composerLockReader)
    {
        $this->composerLockReader = $composerLockReader;
    }

    /**
     * @return array<string, string>
     */
    public function fetchCurrentSprykerModulesState(): array
    {
        $composerLockData = $this->composerLockReader->read();

        $sprykerModules = [];

        foreach ([static::PACKAGES_KEY, static::PACKAGES_DEV_KEY] as $type) {
            if (!isset($composerLockData[$type])) {
                continue;
            }

            $sprykerModules[] = $this->composerPackagesToFlatArray(array_filter(
                $composerLockData[$type],
                static fn (array $package): bool => strpos($package[static::NAME_KEY], static::SPRYKER_PREFIX) === 0
                        && strpos($package[static::NAME_KEY], static::EXCLUDED_VENDOR) === false
            ));
        }

        return array_merge(...$sprykerModules);
    }

    /**
     * @param array<array<string, string>> $composerPackages
     *
     * @return array<string, string>
     */
    protected function composerPackagesToFlatArray(array $composerPackages): array
    {
        $result = [];

        foreach ($composerPackages as $package) {
            $result[$package[static::NAME_KEY]] = $package[static::VERSION_KEY];
        }

        return $result;
    }
}
