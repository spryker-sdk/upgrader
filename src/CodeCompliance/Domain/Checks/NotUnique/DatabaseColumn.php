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

class DatabaseColumn extends AbstractCodeComplianceCheck
{
    /**
     * @var string
     */
    protected const DOCUMENTATION_URL_PATH = 'entity-name-is-not-unique.html#notuniquedatabasecolumn';

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:DatabaseColumn';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Database column %s has to have project prefix %s in %s, like %s_%s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];
        $coreSchemas = [];
        $coreSources = $this->getCodebaseSourceDto()->getDatabaseSchemaCoreCodebaseSources();
        foreach ($coreSources as $coreSource) {
            $coreSchemas[$coreSource->getName()] = array_merge($coreSchemas[$coreSource->getName()] ?? [], $coreSource->getChildElements());
        }

        $namesFromCoreSchemas = array_column($coreSources, static::COLUMN_KEY_NAME);
        $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();
        foreach ($this->getCodebaseSourceDto()->getDatabaseSchemaCodebaseSources() as $source) {
            if ($source->getChildElements() === []) {
                continue;
            }

            $sourceName = $source->getName();
            $columnsWithoutPrefix = array_filter(
                $source->getChildElements(),
                function (string $column) use ($projectPrefixes, $sourceName, $coreSchemas) {
                    return !in_array($column, $coreSchemas[$sourceName] ?? []) && !$this->hasProjectPrefix($column, $projectPrefixes);
                },
            );
            $isDbPrefixExist = $this->hasProjectPrefix($sourceName, $projectPrefixes);
            $isDbUniqueOnTheProjectLevel = !in_array($sourceName, $namesFromCoreSchemas);

            if (
                $columnsWithoutPrefix !== [] &&
                $columnsWithoutPrefix !== null &&
                !$isDbPrefixExist &&
                !$isDbUniqueOnTheProjectLevel
            ) {
                foreach ($columnsWithoutPrefix as $columnWithoutPrefix) {
                    $guideline = sprintf(
                        $this->getGuideline(),
                        $columnWithoutPrefix,
                        implode(',', $projectPrefixes),
                        $source->getPath(),
                        strtolower((string)reset($projectPrefixes)),
                        $columnWithoutPrefix,
                    );
                    $violations[] = new Violation((string)(new Id()), $guideline, $this->getName(), $this->getSeverity(), [
                        static::KEY_ATTRIBUTE_DOCUMENTATION => $this->getDocumentationUrl(),
                    ]);
                }
            }
        }

        return $violations;
    }
}
