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
     * @var string
     */
    protected string $projectPrefix;

    /**
     * @var array<string>
     */
    protected array $excludeList;

    /**
     * @param array<string> $projectPath
     * @param array<string> $corePath
     * @param array<string> $coreNamespaces
     * @param string $projectPrefix
     * @param array<string> $excludeList
     */
    public function __construct(
        array $projectPath = [],
        array $corePath = [],
        array $coreNamespaces = [],
        string $projectPrefix = '',
        array $excludeList = []
    ) {
        $this->projectPath = $projectPath;
        $this->corePath = $corePath;
        $this->coreNamespaces = $coreNamespaces;
        $this->projectPrefix = $projectPrefix;
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
     * @return string
     */
    public function getProjectPrefix(): string
    {
        return $this->projectPrefix;
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
