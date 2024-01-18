<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Shared\Dto;

use ReleaseApp\Application\Configuration\ReleaseAppConstant;
use SprykerSdk\Utils\Infrastructure\Helper\SemanticVersionHelper;

class ModuleDto
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $version;

    /**
     * @var string
     */
    protected string $versionType;

    /**
     * @var array<string, string>
     */
    protected array $featurePackages;

    /**
     * @param string $name
     * @param string $version
     * @param string $versionType
     * @param array<string, string> $featurePackages
     */
    public function __construct(string $name, string $version, string $versionType = ReleaseAppConstant::MODULE_TYPE_MINOR, array $featurePackages = [])
    {
        $this->name = $name;
        $this->version = $version;
        $this->versionType = $versionType;
        $this->featurePackages = $featurePackages;
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
     * @return array<string, string>
     */
    public function getFeaturePackages(): array
    {
        return $this->featurePackages;
    }

    /**
     * @return string
     */
    public function getVersionType(): string
    {
        return $this->versionType;
    }

    /**
     * @return bool
     */
    public function isMajor(): bool
    {
        return $this->versionType === ReleaseAppConstant::MODULE_TYPE_MAJOR;
    }

    /**
     * @return bool
     */
    public function isBetaMajor(): bool
    {
        return $this->isMinor() && SemanticVersionHelper::getMajorVersion($this->version) === 0;
    }

    /**
     * @return bool
     */
    public function isBeta(): bool
    {
        return SemanticVersionHelper::getMajorVersion($this->version) === 0;
    }

    /**
     * @return bool
     */
    public function isMinor(): bool
    {
        return $this->versionType === ReleaseAppConstant::MODULE_TYPE_MINOR;
    }

    /**
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->versionType === ReleaseAppConstant::MODULE_TYPE_PATCH;
    }
}
