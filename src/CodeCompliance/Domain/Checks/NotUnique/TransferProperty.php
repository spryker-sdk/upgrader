<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\NotUnique;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

class TransferProperty extends AbstractCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:TransferProperty';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Transfer property %s for %s has to have project prefix %s in %s, like %s%s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];
        $coreSchemas = [];
        $coreSources = $this->getCodebaseSourceDto()->getTransferSchemaCoreCodebaseSources();

        if ($coreSources !== []) {
            foreach ($coreSources as $coreSource) {
                $coreSchemas[$coreSource->getName()] = array_merge($coreSchemas[$coreSource->getName()] ?? [], $coreSource->getChildElements());
            }
        }

        $projectPrefix = $this->getCodebaseSourceDto()->getProjectPrefix();

        foreach ($this->getCodebaseSourceDto()->getTransferSchemaCodebaseSources() as $transfer) {
            if ($transfer->getChildElements() === []) {
                continue;
            }
            $sourceName = $transfer->getName();
            $propertiesWithoutPrefix = array_filter($transfer->getChildElements(), function (string $property) use ($projectPrefix, $sourceName, $coreSchemas) {
                return !in_array($property, $coreSchemas[$sourceName] ?? []) && stripos($property, $projectPrefix) !== 0;
            });

            if ($propertiesWithoutPrefix !== [] && $propertiesWithoutPrefix !== null) {
                foreach ($propertiesWithoutPrefix as $propertyWithoutPrefix) {
                    $guideline = sprintf($this->getGuideline(), $propertyWithoutPrefix, $transfer->getName(), $projectPrefix, $transfer->getPath(), strtolower($projectPrefix), ucfirst($propertyWithoutPrefix));
                    $violations[] = new Violation(new Id(), $guideline, $this->getName());
                }
            }
        }

        return $violations;
    }
}
