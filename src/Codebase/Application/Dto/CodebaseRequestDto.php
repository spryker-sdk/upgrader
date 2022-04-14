<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

class CodebaseRequestDto
{
    /**
     * @var string
     */
    public const PROJECT_TYPE = 'project';

    /**
     * @var string
     */
    public const CORE_TYPE = 'core';

    /**
     * @var string
     */
    protected string $srcDirectory;

    /**
     * @var string
     */
    protected string $toolingConfigurationPath;

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
     * @var array<string>
     */
    protected array $projectPaths;

    /**
     * @var array<string>
     */
    protected array $projectPrefixes;

    /**
     * @param string $srcDirectory
     * @param string $toolingConfigurationPath
     * @param array<string> $corePaths
     * @param array<string> $coreNamespaces
     * @param array<string> $excludeList
     */
    public function __construct(
        string $srcDirectory = '',
        string $toolingConfigurationPath = '',
        array $corePaths = [],
        array $coreNamespaces = [],
        array $excludeList = []
    ) {
        $this->srcDirectory = $srcDirectory;
        $this->toolingConfigurationPath = $toolingConfigurationPath;
        $this->corePaths = $corePaths;
        $this->coreNamespaces = $coreNamespaces;
        $this->excludeList = $excludeList;
        $this->projectPaths = [];
        $this->projectPrefixes = [];
    }

    /**
     * @return array<string>
     */
    public function getProjectPaths(): array
    {
        if ($this->projectPaths == []) {
            $this->projectPaths = $this->getProjectDirectories($this->getSrcDirectory(), $this->getProjectPrefixes());
        }

        return $this->projectPaths;
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
    public function getProjectPrefixes(): array
    {
        return $this->projectPrefixes;
    }

    /**
     * @return array<string, array<string>>
     */
    public function getPaths(): array
    {
        return [
            static::PROJECT_TYPE => $this->getProjectPaths(),
            static::CORE_TYPE => $this->getCorePaths(),
        ];
    }

    /**
     * @return array<string>
     */
    public function getExcludeList(): array
    {
        return $this->excludeList;
    }

    /**
     * @return string
     */
    public function getToolingConfigurationPath(): string
    {
        return $this->toolingConfigurationPath;
    }

    /**
     * @return string
     */
    public function getSrcDirectory(): string
    {
        return $this->srcDirectory;
    }

    /**
     * @param array<string> $projectPrefixes
     *
     * @return void
     */
    public function setProjectPrefixes(array $projectPrefixes): void
    {
        $this->projectPrefixes = $projectPrefixes;
    }

    /**
     * @param array<string> $projectPaths
     *
     * @return void
     */
    public function setProjectPaths(array $projectPaths): void
    {
        $this->projectPaths = $projectPaths;
    }

    /**
     * @param string $srcDirectory
     * @param array<string> $projectPrefixes
     *
     * @return array<string>
     */
    protected function getProjectDirectories(string $srcDirectory, array $projectPrefixes): array
    {
        $projectDirectories = [];

        foreach ($projectPrefixes as $prefix) {
            $projectDirectories[] = $srcDirectory . $prefix . DIRECTORY_SEPARATOR;
        }

        return $projectDirectories;
    }
}
