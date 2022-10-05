<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\NotUnique;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Checks\Filters\IgnoreListParentFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;

class Constant extends AbstractCodeComplianceCheck
{
    /**
     * @var string
     */
    protected const DOCUMENTATION_URL_PATH = 'entity-name-is-not-unique.html#constant-name-is-not-unique';

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

        $sources = $this->getCodebaseSourceDto()->getPhpCodebaseSources();
        $filteredSources = $this->filterService->filter($sources, [IgnoreListParentFilter::IGNORE_LIST_PARENT_FILTER]);

        foreach ($filteredSources as $source) {
            $coreParent = $source->getCoreParent();
            $parentConstants = $coreParent ? $coreParent->getConstants() : [];
            $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

            foreach ($source->getConstants() as $nameConstant => $valueConstant) {
                $isConstantUnique = !$parentConstants || !array_key_exists($nameConstant, $parentConstants);
                $hasProjectPrefix = $this->hasProjectPrefix($nameConstant, $projectPrefixes);

                if ($coreParent && $isConstantUnique && !$hasProjectPrefix) {
                    $guideline = sprintf(
                        $this->getGuideline(),
                        $source->getClassName(),
                        $nameConstant,
                        strtoupper((string)reset($projectPrefixes)),
                        $nameConstant,
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
