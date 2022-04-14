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
     * @param array<string> $projectPrefixes
     */
    public function __construct(array $projectPrefixes)
    {
        $this->projectPrefixes = $projectPrefixes;
    }

    /**
     * @return array<string>
     */
    public function getProjectPrefixes(): array
    {
        return $this->projectPrefixes;
    }
}
