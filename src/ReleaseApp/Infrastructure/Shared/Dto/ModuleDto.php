<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Shared\Dto;

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
