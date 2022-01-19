<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\Filters\Filters;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use ReflectionMethod;

class DependencyProvider extends AbstractUsedCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:Dependency';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Please avoid usage of %s::%s in %s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $filteredSources = $this->filterService->filter($this->getCodebaseSourceDto()->getPhpCodebaseSources(), [
            Filters::BUSINESS_FACTORY_FILTER,
        ]);
        $violations = [];

        foreach ($filteredSources as $source) {
            $methods = $this->filterMethodDeclaredOnProjectLevel($this->getCodebaseSourceDto()->getCoreNamespaces(), $source->getMethods());

            /** @var \ReflectionMethod $method */
            foreach ($methods as $method) {
                $methodBody = $this->getMethodBody($method);

                if (!$methodBody) {
                    continue;
                }

                $argumentString = $this->getProvidedDependencyGetterArgument($methodBody);

                if (!$argumentString) {
                    continue;
                }

                $constantName = $this->getConstantNameFromConstantUsage($argumentString);

                if (!$constantName) {
                    $guideline = sprintf('Please create constant from %s in %s', $argumentString, $source->getClassName());
                    $violations[] = new Violation(new Id(), $guideline, $this->getName());

                    continue;
                }

                $constantClassName = $this->getClassNameFromConstantUsage($argumentString);

                if (!$constantClassName || $constantClassName === 'static' || $constantClassName === 'self') {
                    $guideline = sprintf('Please use constant from DependencyProvider instead of %s in %s', $argumentString, $source->getClassName());
                    $violations[] = new Violation(new Id(), $guideline, $this->getName());

                    continue;
                }

                $classFileBody = $this->getClassFileBody($method->getDeclaringClass());

                if (!$classFileBody) {
                    continue;
                }

                $constantClassNamespace = $this->getClassNamespace(
                    $classFileBody,
                    $method->getDeclaringClass()->getNamespaceName(),
                    $constantClassName,
                );

                if ($this->hasCoreNamespace($this->getCodebaseSourceDto()->getCoreNamespaces(), $constantClassNamespace)) {
                    $guideline = sprintf($this->getGuideline(), $constantClassName, $constantName, $source->getClassName());
                    $violations[] = new Violation(new Id(), $guideline, $this->getName());

                    continue;
                }

                $constantClassTransfer = $this->getCodebaseSourceDto()->getPhpCodebaseSources()[$constantClassNamespace] ?? null;

                if ($constantClassTransfer !== null) {
                    $coreParent = $constantClassTransfer->getCoreParent();
                    $parentConstants = $coreParent ? $coreParent->getReflection()->getConstants() : [];

                    if ($this->isContainsConstantByName($parentConstants, $constantName)) {
                        $guideline = sprintf($this->getGuideline(), $constantClassName, $constantName, $source->getClassName());
                        $violations[] = new Violation(new Id(), $guideline, $this->getName());
                    }
                }
            }
        }

        return $violations;
    }

    /**
     * @param array<string> $coreNamespaces
     * @param array $methods
     *
     * @return array
     */
    protected function filterMethodDeclaredOnProjectLevel(array $coreNamespaces, array $methods): array
    {
        return array_filter($methods, function (ReflectionMethod $method) use ($coreNamespaces) {
            $namespace = $method->getDeclaringClass()->getNamespaceName();

            return !$this->hasCoreNamespace($coreNamespaces, $namespace);
        });
    }

    /**
     * @param string $usage
     *
     * @return string|null
     */
    protected function getConstantNameFromConstantUsage(string $usage): ?string
    {
        $result = explode('::', $usage);

        if (isset($result[1])) {
            return $result[1];
        }

        return null;
    }

    /**
     * @param string $usage
     *
     * @return string|null
     */
    protected function getClassNameFromConstantUsage(string $usage): ?string
    {
        $result = explode('::', $usage);

        if (isset($result[0])) {
            return $result[0];
        }

        return null;
    }

    /**
     * @param string $fileBody
     * @param string $defaultNamespace
     * @param string $className
     *
     * @return string
     */
    protected function getClassNamespace(string $fileBody, string $defaultNamespace, string $className): string
    {
        $results = [];
        preg_match('/^use (.*)' . $className . ';$/m', $fileBody, $results);
        if (isset($results[0])) {
            return str_replace(['use ', ' as ', ';'], '', $results[0]);
        }

        return $defaultNamespace . '\\' . $className;
    }

    /**
     * @param array $constants
     * @param string $searchName
     *
     * @return bool
     */
    protected function isContainsConstantByName(array $constants, string $searchName): bool
    {
        foreach ($constants as $name => $value) {
            if ($name == $searchName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $source
     *
     * @return string|null
     */
    protected function getProvidedDependencyGetterArgument(string $source): ?string
    {
        $results = [];
        preg_match('/this->getProvidedDependency\((.*)\)/', $source, $results);

        if (isset($results[1])) {
            return $results[1];
        }

        return null;
    }
}
