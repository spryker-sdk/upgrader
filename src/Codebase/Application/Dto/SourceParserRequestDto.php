<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

class SourceParserRequestDto
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
     * @var array<string>
     */
    protected array $projectPaths;

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
    protected array $projectPrefixes;

    /**
     * @var array<string>
     */
    protected array $excludeList;

    /**
     * @param array<string> $projectPaths
     * @param array<string> $corePaths
     * @param array<string> $coreNamespaces
     * @param array<string> $projectPrefixes
     * @param array<string> $excludeList
     */
    public function __construct(
        array $projectPaths,
        array $corePaths,
        array $coreNamespaces,
        array $projectPrefixes,
        array $excludeList = []
    ) {
        $this->projectPaths = $projectPaths;
        $this->corePaths = $corePaths;
        $this->coreNamespaces = $coreNamespaces;
        $this->projectPrefixes = $projectPrefixes;
        $this->excludeList = $excludeList;
    }

    /**
     * @return array<string>
     */
    public function getProjectPaths(): array
    {
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
}
