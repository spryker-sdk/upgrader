<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\NotUnique;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

class ModuleName extends AbstractCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:ModuleName';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Module %s has to have project prefix, like %s%s.';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];
        $coreModuleNames = $this->getCodebaseSourceDto()->getCoreModuleNames();
        $projectModuleNames = $this->getCodebaseSourceDto()->getProjectModuleNames();
        $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

        foreach ($projectModuleNames as $projectModuleName) {
            $hasProjectPrefix = $this->hasProjectPrefix($projectModuleName, $projectPrefixes);
            if (in_array($this->camelCaseToSnakeCase($projectModuleName), $coreModuleNames) || $hasProjectPrefix) {
                continue;
            }

            $guideline = sprintf(
                $this->getGuideline(),
                $projectModuleName,
                reset($projectPrefixes),
                $projectModuleName,
            );
            $violations[] = new Violation(new Id(), $guideline, $this->getName());
        }

        return $violations;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    protected function camelCaseToSnakeCase(string $input): string
    {
        return strtolower((string)preg_replace('/(?<!^)[A-Z]/', '-$0', $input));
    }
}
