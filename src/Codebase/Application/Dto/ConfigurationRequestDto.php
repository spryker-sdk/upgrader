<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

class ConfigurationRequestDto
{
    /**
     * @var string
     */
    protected string $configurationFilePath;

    /**
     * @var string
     */
    protected string $srcDirectory;

    /**
     * @param string $configurationFilePath
     * @param string $srcDirectory
     */
    public function __construct(string $configurationFilePath, string $srcDirectory)
    {
        $this->configurationFilePath = $configurationFilePath;
        $this->srcDirectory = $srcDirectory;
    }

    /**
     * @return string
     */
    public function getConfigurationFilePath(): string
    {
        return $this->configurationFilePath;
    }

    /**
     * @return string
     */
    public function getSrcDirectory(): string
    {
        return $this->srcDirectory;
    }
}
