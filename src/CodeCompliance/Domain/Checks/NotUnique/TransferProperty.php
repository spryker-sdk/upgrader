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
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;

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
     * @return string
     */
    public function getDocumentationUrl(): string
    {
        return static::DOCUMENTATION_BASE_URL . 'entity-name-is-not-unique.html#transfer-property-name-is-not-unique';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];
        $coreSchemas = [];

        $coreCodebaseSources = $this->getCodebaseSourceDto()->getTransferSchemaCoreCodebaseSources();
        $nameFromCoreTransfers = array_column($coreCodebaseSources, static::COLUMN_KEY_NAME);

        foreach ($coreCodebaseSources as $coreSource) {
            $coreSchemas[$coreSource->getName()] = array_merge($coreSchemas[$coreSource->getName()] ?? [], $coreSource->getChildElements());
        }

        $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();
        foreach ($this->getCodebaseSourceDto()->getTransferSchemaCodebaseSources() as $transfer) {
            if ($transfer->getChildElements() === []) {
                continue;
            }
            $transferName = $transfer->getName();
            $propertiesWithoutPrefix = array_filter(
                $transfer->getChildElements(),
                function (string $property) use ($projectPrefixes, $transferName, $coreSchemas) {
                    return !in_array($property, $coreSchemas[$transferName] ?? []) && !$this->hasProjectPrefix($property, $projectPrefixes);
                },
            );
            $isTransferPrefixExist = $this->hasProjectPrefix($transfer->getName(), $projectPrefixes);
            $isTransferUniqueOnTheProjectLevel = !in_array($transfer->getName(), $nameFromCoreTransfers);

            if (
                $propertiesWithoutPrefix !== [] &&
                $propertiesWithoutPrefix !== null &&
                !$isTransferPrefixExist &&
                !$isTransferUniqueOnTheProjectLevel
            ) {
                foreach ($propertiesWithoutPrefix as $propertyWithoutPrefix) {
                    $guideline = sprintf(
                        $this->getGuideline(),
                        $propertyWithoutPrefix,
                        $transfer->getName(),
                        implode(',', $projectPrefixes),
                        $transfer->getPath(),
                        strtolower((string)reset($projectPrefixes)),
                        ucfirst($propertyWithoutPrefix),
                    );
                    $violations[] = new Violation((string)(new Id()), $guideline, $this->getName(), ViolationInterface::SEVERITY_ERROR, [
                        static::KEY_ATTRIBUTE_DOCUMENTATION => $this->getDocumentationUrl(),
                    ]);
                }
            }
        }

        return $violations;
    }
}
