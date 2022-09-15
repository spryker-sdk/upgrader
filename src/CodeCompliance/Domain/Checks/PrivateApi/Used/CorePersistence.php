<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\Filters\IgnoreListParentFilter;
use CodeCompliance\Domain\Checks\Filters\PersistenceFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

class CorePersistence extends AbstractUsedCodeComplianceCheck
{
    /**
     * @var array<string>
     */
    protected const IGNORE_METHODS_LIST = ['getFactory'];

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:CorePersistenceUsage';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Please avoid usage of PrivateApi method %s::%s()';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $filteredSources = $this->filterService->filter($this->getCodebaseSourceDto()->getPhpCodebaseSources(), [
            PersistenceFilter::PERSISTENCE_FILTER, IgnoreListParentFilter::IGNORE_LIST_PARENT_FILTER,
        ]);

        $violations = [];

        foreach ($filteredSources as $source) {
            $classFileBody = $this->getClassFileBody($source->getReflection());
            if (!$classFileBody) {
                continue;
            }
            $usedMethodNames = $this->parseUsedMethods($classFileBody);

            foreach ($usedMethodNames as $methodName) {
                if (in_array($methodName, static::IGNORE_METHODS_LIST)) {
                    continue;
                }
                $methodReflection = $source->getReflection()->getMethod($methodName);

                if (
                    $this->hasCoreNamespace(
                        $this->getCodebaseSourceDto()->getCoreNamespaces(),
                        $methodReflection->getDeclaringClass()->getName(),
                    )
                ) {
                    $guideline = sprintf($this->getGuideline(), $source->getClassName(), $methodName);
                    $violations[] = new Violation((string)(new Id()), $guideline, $this->getName());
                }
            }
        }

        return $violations;
    }
}
