<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Application\Dto;

class ConfigurationResponseDto
{
    /**
     * @var array<string>
     */
    protected array $projectPrefixes = ['Pyz'];

    /**
     * @var array<string>
     */
    protected array $ignoredRules = [];

    /**
     * @param array<string> $projectPrefixes
     */
    public function setProjectPrefixes(array $projectPrefixes): void
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

    /**
     * @return array<string>
     */
    public function getIgnoredRules(): array
    {
        return $this->ignoredRules;
    }

    /**
     * @param array<string> $ignoredRules
     */
    public function setIgnoredRules(array $ignoredRules): void
    {
        $this->ignoredRules = $ignoredRules;
    }
}
