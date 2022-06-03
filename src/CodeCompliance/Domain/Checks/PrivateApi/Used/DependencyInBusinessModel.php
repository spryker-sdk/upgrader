<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Application\Dto\MethodCodebaseDto;
use CodeCompliance\Domain\Checks\Filters\BusinessModelFilter;
use CodeCompliance\Domain\Checks\Filters\PrivateApiFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use Exception;

class DependencyInBusinessModel extends AbstractUsedCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:PrivateApiDependencyInBusinessModel';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Please avoid usage of %s in %s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $filteredSources = $this->filterService->filter($this->getCodebaseSourceDto()->getPhpCodebaseSources(), [
            BusinessModelFilter::BUSINESS_MODEL_FILTER,
        ]);

        $violations = [];
        /** @var \Codebase\Application\Dto\ClassCodebaseDto $source */
        foreach ($filteredSources as $source) {
            $reflectionMethod = $this->getMethodFromCurrentFile($source, '__construct');
            if (!$reflectionMethod) {
                continue;
            }

            $constructorDoc = $reflectionMethod->getDocComment();
            if (!$constructorDoc) {
                continue;
            }

            $params = $this->getParamNamespacesFromDocComment($constructorDoc);
            $dependencyNamespaces = $this->skipBasicTypes($params);
            $dependencyCoreSources = $this->getCoreSourcesByNamespaces(
                $dependencyNamespaces,
                $this->getCodebaseSourceDto()->getPhpCoreCodebaseSources(),
            );
            $dependencyCoreSources = $this->filterService->filter($dependencyCoreSources, [
                PrivateApiFilter::PRIVATE_API_FILTER,
            ]);

            if (!count($dependencyCoreSources)) {
                continue;
            }
            /** @var \Codebase\Application\Dto\ClassCodebaseDto $class */
            foreach ($dependencyCoreSources as $class) {
                $guideline = sprintf($this->getGuideline(), $class->getName(), $source->getName());
                $violations[] = new Violation(new Id(), $guideline, $this->getName());
            }
        }

        return $violations;
    }

    /**
     * @param \Codebase\Application\Dto\ClassCodebaseDto $classCodebaseDto
     * @param string $methodName
     *
     * @return \ReflectionMethod|null
     */
    protected function getMethodFromCurrentFile(ClassCodebaseDto $classCodebaseDto, string $methodName): ?MethodCodebaseDto
    {
        try {
            $methodCodebaseDto = $classCodebaseDto->getMethod($methodName);
            if ($methodCodebaseDto->getDeclaringClass()->getName() != $classCodebaseDto->getName()) {
                $methodCodebaseDto = null;
            }
        } catch (Exception $exception) {
            $methodCodebaseDto = null;
        }

        return $methodCodebaseDto ?? null;
    }

    /**
     * @param array<string> $params
     *
     * @return array<string>
     */
    protected function skipBasicTypes(array $params): array
    {
        return array_filter($params, function ($param) {
            return !in_array($param, ['int', 'string', 'bool', 'array', 'ArrayObject']);
        });
    }

    /**
     * @param array<string> $dependencyNamespaces
     * @param array<mixed> $sources
     *
     * @return array<string, \Codebase\Application\Dto\CodebaseInterface>
     */
    protected function getCoreSourcesByNamespaces(array $dependencyNamespaces, array $sources): array
    {
        $results = [];

        foreach ($dependencyNamespaces as $namespace) {
            $namespace = ltrim($namespace, '\\');
            if (isset($sources[$namespace])) {
                $results[$namespace] = $sources[$namespace];
            }
        }

        return $results;
    }
}
