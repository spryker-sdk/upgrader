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

class TransferName extends AbstractCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:TransferName';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Transfer object name %s has to have project prefix %s in %s, like %s%s';
    }

    /**
     * @return string
     */
    public function getDocumentationUrl(): string
    {
        return static::DOCUMENTATION_BASE_URL . 'entity-name-is-not-unique.html#transfer-name-is-not-unique';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];
        $nameFromCoreTransfers = [];

        if ($this->getCodebaseSourceDto()->getTransferSchemaCoreCodebaseSources()) {
            $nameFromCoreTransfers = array_column($this->getCodebaseSourceDto()->getTransferSchemaCoreCodebaseSources(), static::COLUMN_KEY_NAME);
        }
        $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

        foreach ($this->getCodebaseSourceDto()->getTransferSchemaCodebaseSources() as $transfer) {
            $isTransferUniqueOnTheProjectLevel = !in_array($transfer->getName(), $nameFromCoreTransfers);
            $isTransferPrefixExist = $this->hasProjectPrefix($transfer->getName(), $projectPrefixes);

            if ($isTransferUniqueOnTheProjectLevel && !$isTransferPrefixExist) {
                $guideline = sprintf(
                    $this->getGuideline(),
                    $transfer->getName(),
                    implode(',', $projectPrefixes),
                    $transfer->getPath(),
                    reset($projectPrefixes),
                    $transfer->getName(),
                );
                $violations[] = new Violation((string)(new Id()), $guideline, $this->getName(), ViolationInterface::SEVERITY_ERROR, [
                    'documentation' => $this->getDocumentationUrl(),
                ]);
            }
        }

        return $violations;
    }
}
