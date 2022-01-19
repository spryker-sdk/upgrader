<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Evaluate\Infrastructure\Configuration;

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
    public function getProjectPrefix(): string
    {
        return (string)getenv('PROJECT_PREFIX') ?: 'Pyz';
    }

    /**
     * @return array
     */
    public function getProjectDirectory(): array
    {
        return [
            $this->getSrcDirectory() . $this->getProjectPrefix() . DIRECTORY_SEPARATOR,
        ];
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
     * @return array
     */
    public function getCoreDirectory(): array
    {
        return [
            $this->getVendorDirectory() . 'spryker' . DIRECTORY_SEPARATOR,
            $this->getVendorDirectory() . 'spryker-eco' . DIRECTORY_SEPARATOR,
            $this->getVendorDirectory() . 'spryker-shop' . DIRECTORY_SEPARATOR,
        ];
    }

    /**
     * @return string
     */
    protected function getVendorDirectory(): string
    {
        return $this->getRootDirectory() . 'vendor' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getRootDirectory(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getSrcDirectory(): string
    {
        return $this->getRootDirectory() . 'src' . DIRECTORY_SEPARATOR;
    }
}
