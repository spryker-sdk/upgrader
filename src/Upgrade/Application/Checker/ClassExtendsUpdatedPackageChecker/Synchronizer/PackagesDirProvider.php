<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer;

use Upgrade\Infrastructure\IO\Filesystem;
use Upgrader\Configuration\ConfigurationProvider;

class PackagesDirProvider implements PackagesDirProviderInterface
{
    /**
     * @var string
     */
    protected const FROM_DIR = 'vendor';

    /**
     * @var string
     */
    protected const TO_DIR = 'data' . DIRECTORY_SEPARATOR . 'upgrader' . DIRECTORY_SEPARATOR . 'vendor_prev';

    /**
     * @var string
     */
    protected const SPRYKER_PACKAGE_PREFIX = 'spryker';

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Upgrade\Infrastructure\IO\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \Upgrade\Infrastructure\IO\Filesystem $filesystem
     */
    public function __construct(ConfigurationProvider $configurationProvider, Filesystem $filesystem)
    {
        $this->configurationProvider = $configurationProvider;
        $this->filesystem = $filesystem;
    }

    /**
     * @return array<string>
     */
    public function getSprykerPackageDirs(): array
    {
        $fromDir = $this->getFromDir();

        $packagesDirs = $this->filesystem->scanDir($fromDir);

        return array_values(array_filter(
            $packagesDirs,
            static fn (string $dir): bool => strpos($dir, static::SPRYKER_PACKAGE_PREFIX) === 0
        ));
    }

    /**
     * @return string
     */
    public function getFromDir(): string
    {
        return $this->configurationProvider->getRootPath() . static::FROM_DIR . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getToDir(): string
    {
        return $this->configurationProvider->getRootPath() . static::TO_DIR . DIRECTORY_SEPARATOR;
    }
}
