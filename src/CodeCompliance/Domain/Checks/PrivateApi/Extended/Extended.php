<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\PrivateApi\Extended;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Checks\Filters\BusinessModelFilter;
use CodeCompliance\Domain\Checks\Filters\CoreExtensionFilter;
use CodeCompliance\Domain\Checks\Filters\IgnoreListFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

class Extended extends AbstractCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:Extension';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Please avoid extension of the PrivateApi %s in %s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $sources = $this->getCodebaseSourceDto()->getPhpCodebaseSources();
        $filteredSources = $this->filterService->filter($sources, [
            BusinessModelFilter::BUSINESS_MODEL_FILTER,
            CoreExtensionFilter::CORE_EXTENSION_FILTER,
            IgnoreListFilter::IGNORE_LIST_FILTER,
        ]);

        $violations = [];

        foreach ($filteredSources as $source) {
            $coreParent = $source->getCoreParent();

            if (!$coreParent) {
                continue;
            }

            $guideline = sprintf($this->getGuideline(), $coreParent->getClassName(), $source->getClassName());
            $violations[] = new Violation(new Id(), $guideline, $this->getName());
        }

        return $violations;
    }
}
