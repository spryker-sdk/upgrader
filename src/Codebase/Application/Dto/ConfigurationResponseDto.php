<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

class ConfigurationResponseDto
{
    /**
     * @var array<string>
     */
    protected array $projectPrefixes;

    /**
     * @var array<string>
     */
    protected array $projectDirectories;

    /**
     * @param array<string> $projectPrefixes
     * @param array<string> $projectDirectories
     */
    public function __construct(array $projectPrefixes, array $projectDirectories)
    {
        $this->projectPrefixes = $projectPrefixes;
        $this->projectDirectories = $projectDirectories;
    }

    /**
     * @return array<string>
     */
    public function getProjectPrefixes(): array
    {
        return $this->projectPrefixes;
    }

    /**
     * @return array<string>
     */
    public function getProjectDirectories(): array
    {
        return $this->projectDirectories;
    }
}
