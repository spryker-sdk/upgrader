<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader\Configuration;

class ConfigurationProvider
{
    /**
     * @var array<string>
     */
    protected const CORE_NAMESPACES = ['SprykerShop', 'SprykerEco', 'Spryker', 'SprykerSdk'];

    /**
     * @return array<string>
     */
    public function getCoreNamespaces(): array
    {
        return static::CORE_NAMESPACES;
    }

    /**
     * @return string
     */
    public function getToolingConfigurationFilePath(): string
    {
        return (string)getenv('PROJECT_CONFIGURATION_FILE_PATH') ?: 'tooling.yml';
    }

    /**
     * @return array<string>
     */
    public function getIgnoreSources(): array
    {
        return [
            'Zed/DataImport',
            'SprykerTest',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getCorePaths(): array
    {
        $directories = (array)glob($this->getVendorPath() . 'spryker*', GLOB_ONLYDIR);

        return array_filter($directories, function ($directory) {
            return $directory !== false;
        });
    }

    /**
     * @return string
     */
    public function getSrcPath(): string
    {
        return $this->getRootPath() . 'src' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getVendorPath(): string
    {
        return $this->getRootPath() . 'vendor' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getRootPath(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR;
    }
}
