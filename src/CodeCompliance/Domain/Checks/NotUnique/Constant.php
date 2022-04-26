<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\NotUnique;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

class Constant extends AbstractCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:Constant';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return '%s::%s name has to have project namespace, like %s_%s.';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];

        foreach ($this->getCodebaseSourceDto()->getPhpCodebaseSources() as $source) {
            $coreParent = $source->getCoreParent();
            $parentConstants = $coreParent ? $coreParent->getConstants() : [];
            $projectPrefixList = $this->getCodebaseSourceDto()->getProjectPrefixes();

            foreach ($source->getConstants() as $nameConstant => $valueConstant) {
                $isConstantUnique = !$parentConstants || !array_key_exists($nameConstant, $parentConstants);
                $hasProjectPrefix = $this->hasProjectPrefix($nameConstant, $projectPrefixList);

                if ($coreParent && $isConstantUnique && !$hasProjectPrefix) {
                    $guideline = sprintf(
                        $this->getGuideline(),
                        $source->getClassName(),
                        $nameConstant,
                        strtoupper((string)reset($projectPrefixList)),
                        $nameConstant,
                    );
                    $violations[] = new Violation(new Id(), $guideline, $this->getName());
                }
            }
        }

        return $violations;
    }
}
