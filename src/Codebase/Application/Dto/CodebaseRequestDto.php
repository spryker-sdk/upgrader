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
     * @var array<string>
     */
    protected array $projectPath;

    /**
     * @var array<string>
     */
    protected array $corePath;

    /**
     * @var array<string>
     */
    protected array $coreNamespaces;

    /**
     * @var array
     */
    protected array $projectPrefixList;

    /**
     * @var array<string>
     */
    protected array $excludeList;

    /**
     * @param array $projectPath
     * @param array $corePath
     * @param array $coreNamespaces
     * @param array $projectPrefixList
     * @param array $excludeList
     */
    public function __construct(
        array $projectPath = [],
        array $corePath = [],
        array $coreNamespaces = [],
        array $projectPrefixList = [],
        array $excludeList = []
    ) {
        $this->projectPath = $projectPath;
        $this->corePath = $corePath;
        $this->coreNamespaces = $coreNamespaces;
        $this->projectPrefixList = $projectPrefixList;
        $this->excludeList = $excludeList;
    }

    /**
     * @return array<string>
     */
    public function getProjectPath(): array
    {
        return $this->projectPath;
    }

    /**
     * @return array<string>
     */
    public function getCorePath(): array
    {
        return $this->corePath;
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
    public function getProjectPrefixList(): array
    {
        return $this->projectPrefixList;
    }

    /**
     * @return array<string, array<string>>
     */
    public function getPaths(): array
    {
        return [
            'project' => $this->getProjectPath(),
            'core' => $this->getCorePath(),
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
