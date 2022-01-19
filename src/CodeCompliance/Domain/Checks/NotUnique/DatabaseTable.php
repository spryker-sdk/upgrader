<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\NotUnique;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

class DatabaseTable extends AbstractCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:DatabaseTable';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Database table %s has to have project prefix %s in %s, like %s_%s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];
        $namesFromCoreSchemas = [];
        $projectPrefix = $this->getCodebaseSourceDto()->getProjectPrefix();

        if ($this->getCodebaseSourceDto()->getDatabaseSchemaCoreCodebaseSources()) {
            $namesFromCoreSchemas = array_column($this->getCodebaseSourceDto()->getDatabaseSchemaCoreCodebaseSources(), static::COLUMN_KEY_NAME);
        }

        foreach ($this->getCodebaseSourceDto()->getDatabaseSchemaCodebaseSources() as $schema) {
            $isDbUniqueOnTheProjectLevel = !in_array($schema->getName(), $namesFromCoreSchemas);
            $isDbPrefixExist = stripos($schema->getName(), $projectPrefix) === 0;

            if ($isDbUniqueOnTheProjectLevel && !$isDbPrefixExist) {
                $guideline = sprintf($this->getGuideline(), $schema->getName(), $projectPrefix, $schema->getPath(), strtolower($projectPrefix), $schema->getName());
                $violations[] = new Violation(new Id(), $guideline, $this->getName());
            }
        }

        return $violations;
    }
}
