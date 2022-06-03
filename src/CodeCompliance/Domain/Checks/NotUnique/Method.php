<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\NotUnique;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Application\Dto\TraitCodebaseDto;
use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Checks\Filters\PluginFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

class Method extends AbstractCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:Method';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Method name %s::%s() should contains project prefix, like %s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $sources = $this->getCodebaseSourceDto()->getPhpCodebaseSources();
        $filteredSources = $this->filterService->filter($sources, [PluginFilter::PLUGIN_FILTER]);

        $violations = [];
        /** @var \Codebase\Application\Dto\ClassCodebaseDto $source */
        foreach ($filteredSources as $source) {
            $namesCoreMethods = array_column($source->getCoreMethods(), static::COLUMN_KEY_NAME);
            $nameCoreInterfaceMethods = array_column($source->getCoreInterfacesMethods(), static::COLUMN_KEY_NAME);
            $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

            foreach ($source->getProjectMethods() as $projectMethod) {
                if ($this->isMagicMethod($projectMethod->getName())) {
                    continue;
                }
                if ($this->isTraitMethod($projectMethod->getName(), $projectMethod->getDeclaringClass())) {
                    continue;
                }
                if ($this->isDependencyProviderPluginStack($projectMethod->getName(), $source->getName())) {
                    continue;
                }
                if ($this->isPluginReturnInDocComment((string)$projectMethod->getDocComment())) {
                    continue;
                }

                $isCoreMethod = in_array($projectMethod->getName(), $namesCoreMethods);
                $isMethodDeclaredInInterface = in_array($projectMethod->getName(), $nameCoreInterfaceMethods);
                $hasProjectPrefix = $this->hasProjectPrefix($projectMethod->getName(), $projectPrefixes);

                if ($source->isExtendCore() && !$isCoreMethod && !$hasProjectPrefix && !$isMethodDeclaredInInterface) {
                    $methodParts = preg_split('/(?=[A-Z])/', $projectMethod->getName()) ?: [];
                    array_splice($methodParts, 1, 0, [reset($projectPrefixes)]);
                    $guideline = sprintf(
                        $this->getGuideline(),
                        $source->getName(),
                        $projectMethod->getName(),
                        lcfirst(implode('', $methodParts)),
                    );
                    $violations[] = new Violation(new Id(), $guideline, $this->getName());
                }
            }
        }

        return $violations;
    }

    /**
     * @param string $methodName
     * @param string $className
     *
     * @return bool
     */
    protected function isDependencyProviderPluginStack(string $methodName, string $className): bool
    {
        return (preg_match('/.*Factory$/', $className) || preg_match('/.*DependencyProvider$/', $className))
            && preg_match('/.*Plugins$/', $methodName);
    }

    /**
     * @param string $methodName
     *
     * @return bool
     */
    protected function isMagicMethod(string $methodName): bool
    {
        return strpos($methodName, '__') === 0;
    }

    /**
     * @param string $method
     * @param \Codebase\Application\Dto\ClassCodebaseDto $class
     *
     * @return bool
     */
    protected function isTraitMethod(string $method, ClassCodebaseDto $class): bool
    {
        $traits = array_filter($class->getTraits(), function (ClassCodebaseDto $trait) use ($method) {
            $traitMethods = array_column($trait->getMethods(), static::COLUMN_KEY_NAME);

            return in_array($method, $traitMethods);
        });

        return count($traits) > 0;
    }

    /**
     * @param string $value
     * @param array<string> $projectPrefixes
     *
     * @return bool
     */
    protected function hasProjectPrefix(string $value, array $projectPrefixes): bool
    {
        foreach ($projectPrefixes as $projectPrefix) {
            if (strpos($value, $projectPrefix) !== false) {
                return true;
            }
        }

        return false;
    }
}
