<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\NotUnique;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use ReflectionClass;

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
        return 'Method name %s::%s() should contains project prefix, like %s%s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];

        foreach ($this->getCodebaseSourceDto()->getPhpCodebaseSources() as $source) {
            $namesCoreMethods = array_column($source->getCoreMethods(), static::COLUMN_KEY_NAME);
            $interfaceMethods = $this->getInterfaceMethods($source->getInterfaces());
            $namesInterfaceMethods = array_column($interfaceMethods, static::COLUMN_KEY_NAME);
            $projectPrefix = $this->getCodebaseSourceDto()->getProjectPrefix();

            /** @var \ReflectionMethod $projectMethod */
            foreach ($source->getProjectMethods() as $projectMethod) {
                if ($this->isMagicMethod($projectMethod->getName())) {
                    continue;
                }
                if ($this->isTraitMethod($projectMethod->getName(), $projectMethod->getDeclaringClass())) {
                    continue;
                }
                if ($this->isDependencyProviderPluginStack($projectMethod->getName(), $source->getClassName())) {
                    continue;
                }

                $isCoreMethod = in_array($projectMethod->getName(), $namesCoreMethods);
                $isMethodDeclaredInInterface = in_array($projectMethod->getName(), $namesInterfaceMethods);
                $hasProjectPrefix = $this->hasProjectPrefix($projectMethod->getName(), $projectPrefix);

                if (!$isCoreMethod && !$hasProjectPrefix && !$isMethodDeclaredInInterface) {
                    $methodParts = preg_split('/(?=[A-Z])/', $projectMethod->getName()) ?: [];
                    array_splice($methodParts, 1, 0, [$projectPrefix]);
                    $guideline = sprintf($this->getGuideline(), $source->getClassName(), $projectMethod->getName(), strtolower($projectPrefix), ucfirst(implode('', $methodParts)));
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
     * @param array<\ReflectionClass> $interfaces
     *
     * @return array<\ReflectionMethod>
     */
    protected function getInterfaceMethods(array $interfaces): array
    {
        $methods = [];
        foreach ($interfaces as $interface) {
            $methods = array_merge($methods, $interface->getMethods());
        }

        return $methods;
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
     * @param \ReflectionClass $class
     *
     * @return bool
     */
    protected function isTraitMethod(string $method, ReflectionClass $class): bool
    {
        $traits = array_filter($class->getTraits(), function ($trait) use ($method) {
            $traitMethods = array_column($trait->getMethods(), static::COLUMN_KEY_NAME);

            return in_array($method, $traitMethods);
        });

        return count($traits) > 0;
    }
}
