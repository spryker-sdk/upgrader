<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Dto\Composer;

class PackageDto
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
    protected string $previousVersion;

    /**
     * @var string
     */
    protected string $diffLink;

    /**
     * @param string $name
     * @param string $version
     * @param string $previousVersion
     * @param string $diffLink
     */
    public function __construct(
        string $name = '',
        string $version = '',
        string $previousVersion = '',
        string $diffLink = ''
    ) {
        $this->name = $name;
        $this->version = $version;
        $this->previousVersion = $previousVersion;
        $this->diffLink = $diffLink;
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
    public function __toString(): string
    {
        return sprintf('%s:%s', $this->getName(), $this->getVersion());
    }

    /**
     * @return string
     */
    public function getPreviousVersion(): string
    {
        return $this->previousVersion;
    }

    /**
     * @return string
     */
    public function getDiffLink(): string
    {
        return $this->diffLink;
    }
}
