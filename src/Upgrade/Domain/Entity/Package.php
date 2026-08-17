<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Domain\Entity;

class Package
{
    protected string $name;

    protected string $version;

    protected string $previousVersion;

    protected string $diffLink;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function __toString(): string
    {
        return sprintf('%s:%s', $this->getName(), $this->getVersion());
    }

    public function getPreviousVersion(): string
    {
        return $this->previousVersion;
    }

    public function getDiffLink(): string
    {
        return $this->diffLink;
    }
}
