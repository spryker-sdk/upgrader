<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\NotUnique;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

class DatabaseTable extends AbstractCodeComplianceCheck
{
    /**
     * @var string
     */
    protected const DOCUMENTATION_URL_PATH = 'entity-name-is-not-unique.html#database-table-name-is-not-unique';

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
        $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

        if ($this->getCodebaseSourceDto()->getDatabaseSchemaCoreCodebaseSources()) {
            $namesFromCoreSchemas = array_column($this->getCodebaseSourceDto()->getDatabaseSchemaCoreCodebaseSources(), static::COLUMN_KEY_NAME);
        }

        foreach ($this->getCodebaseSourceDto()->getDatabaseSchemaCodebaseSources() as $schema) {
            $isDbUniqueOnTheProjectLevel = !in_array($schema->getName(), $namesFromCoreSchemas);
            $isDbPrefixExist = $this->hasProjectPrefix($schema->getName(), $projectPrefixes);

            if ($isDbUniqueOnTheProjectLevel && !$isDbPrefixExist) {
                $guideline = sprintf(
                    $this->getGuideline(),
                    $schema->getName(),
                    implode(',', $projectPrefixes),
                    $schema->getPath(),
                    strtolower((string)reset($projectPrefixes)),
                    $schema->getName(),
                );
                $violations[] = new Violation((string)(new Id()), $guideline, $this->getName(), $this->getSeverity(), [
                    static::KEY_ATTRIBUTE_DOCUMENTATION => $this->getDocumentationUrl(),
                ]);
            }
        }

        return $violations;
    }
}
