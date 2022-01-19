<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\NotUnique;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

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
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];
        $nameFromCoreTransfers = [];

        if ($this->getCodebaseSourceDto()->getTransferSchemaCoreCodebaseSources()) {
            $nameFromCoreTransfers = array_column($this->getCodebaseSourceDto()->getTransferSchemaCoreCodebaseSources(), static::COLUMN_KEY_NAME);
        }
        $projectPrefix = $this->getCodebaseSourceDto()->getProjectPrefix();

        foreach ($this->getCodebaseSourceDto()->getTransferSchemaCodebaseSources() as $transfer) {
            $isTransferUniqueOnTheProjectLevel = !in_array($transfer->getName(), $nameFromCoreTransfers);
            $isTransferPrefixExist = (stripos($transfer->getName(), $projectPrefix) === 0);

            if ($isTransferUniqueOnTheProjectLevel && !$isTransferPrefixExist) {
                $guideline = sprintf($this->getGuideline(), $transfer->getName(), $projectPrefix, $transfer->getPath(), $projectPrefix, $transfer->getName());
                $violations[] = new Violation(new Id(), $guideline, $this->getName());
            }
        }

        return $violations;
    }
}
