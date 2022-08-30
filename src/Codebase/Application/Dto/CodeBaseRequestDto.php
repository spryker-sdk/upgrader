<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

class CodeBaseRequestDto
{
 /**
  * @var string
  */
    protected string $toolingConfigurationPath;

    /**
     * @var string
     */
    protected string $srcPath;

    /**
     * @var array<string>
     */
    protected array $corePaths;

    /**
     * @var array<string>
     */
    protected array $coreNamespaces;

    /**
     * @var array<string>
     */
    protected array $excludeList;

    /**
     * @var array<\Codebase\Application\Dto\ModuleDto>
     */
    protected array $modules;

    /**
     * @param string $toolingConfigurationPath
     * @param string $srcPath
     * @param array<string> $corePaths
     * @param array<string> $coreNamespaces
     * @param array<string> $excludeList
     * @param array<\Codebase\Application\Dto\ModuleDto> $modules
     */
    public function __construct(
        string $toolingConfigurationPath,
        string $srcPath,
        array $corePaths,
        array $coreNamespaces,
        array $excludeList = [],
        array $modules = []
    ) {
        $this->toolingConfigurationPath = $toolingConfigurationPath;
        $this->srcPath = $srcPath;
        $this->corePaths = $corePaths;
        $this->coreNamespaces = $coreNamespaces;
        $this->excludeList = $excludeList;
        $this->modules = $modules;
    }

    /**
     * @return string
     */
    public function getToolingConfigurationFilePath(): string
    {
        return $this->toolingConfigurationPath;
    }

    /**
     * @return string
     */
    public function getSrcPath(): string
    {
        return $this->srcPath;
    }

    /**
     * @return array<string>
     */
    public function getCorePaths(): array
    {
        return $this->corePaths;
    }

    /**
     * @return array<string>
     */
    public function getCoreNamespaces(): array
    {
        return $this->coreNamespaces;
    }

    /**
     * @return array<string>
     */
    public function getExcludeList(): array
    {
        return $this->excludeList;
    }

    /**
     * @return array<\Codebase\Application\Dto\ModuleDto>
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
