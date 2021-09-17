<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Entity;

class Module
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $versionType;

    /**
     * @param string $name
     * @param string $version
     * @param string $versionType
     */
    public function __construct(string $name, string $version, string $versionType)
    {
        $this->name = $name;
        $this->version = $version;
        $this->versionType = $versionType;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getVersionType(): string
    {
        return $this->versionType;
    }
}
