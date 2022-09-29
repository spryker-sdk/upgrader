<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\Filters\BusinessModelFilter;
use CodeCompliance\Domain\Checks\Filters\IgnoreListFilter;
use CodeCompliance\Domain\Checks\Filters\PrivateApiFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;

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
     * @return string
     */
    public function getDocumentationUrl(): string
    {
        return static::DOCUMENTATION_BASE_URL . 'private-api-is-extended.html#example-of-code-that-causes-an-upgradability-error-extending-a-private-api-business-model';
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

        foreach ($filteredSources as $source) {
            $reflectionMethod = $this->getMethodFromCurrentFile($source->getReflection(), '__construct');
            if (!$reflectionMethod) {
                continue;
            }

            $constructorDoc = $reflectionMethod->getDocComment();
            if (!$constructorDoc) {
                continue;
            }

            $params = $this->getParamNamespacesFromDocComment($constructorDoc);
            $dependencyNamespaces = $this->skipBasicTypes($params);
            $dependencyCoreSources = $this->getCoreSourcesByNamespaces($dependencyNamespaces);
            $dependencyCoreSources = $this->filterService->filter($dependencyCoreSources, [
                PrivateApiFilter::PRIVATE_API_FILTER,
                IgnoreListFilter::IGNORE_LIST_FILTER,
            ]);
            if (!count($dependencyCoreSources)) {
                continue;
            }

            foreach ($dependencyCoreSources as $class) {
                $guideline = sprintf($this->getGuideline(), $class->getClassName(), $source->getClassName());
                $violations[] = new Violation((string)(new Id()), $guideline, $this->getName(), ViolationInterface::SEVERITY_ERROR, [
                    static::KEY_ATTRIBUTE_DOCUMENTATION => $this->getDocumentationUrl(),
                ]);
            }
        }

        return $violations;
    }

    /**
     * @phpstan-template T of object
     *
     * @param \ReflectionClass<T> $reflectionClass
     * @param string $methodName
     *
     * @return \ReflectionMethod|null
     */
    protected function getMethodFromCurrentFile(ReflectionClass $reflectionClass, string $methodName): ?ReflectionMethod
    {
        try {
            $reflectionMethod = $reflectionClass->getMethod($methodName);
            if ($reflectionMethod->getDeclaringClass()->getName() != $reflectionClass->getName()) {
                $reflectionMethod = null;
            }
        } catch (Exception $exception) {
            $reflectionMethod = null;
        }

        return $reflectionMethod ?? null;
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
     *
     * @return array<int, \Codebase\Application\Dto\CodebaseInterface>
     */
    protected function getCoreSourcesByNamespaces(array $dependencyNamespaces): array
    {
        $results = [];

        foreach ($dependencyNamespaces as $namespace) {
            $namespace = ltrim($namespace, '\\');

            if ($this->hasProjectPrefix($namespace, $this->getCodebaseSourceDto()->getProjectPrefixes())) {
                continue;
            }

            $classTransfer = $this->codeBaseService->parsePhpClass(
                $namespace,
                $this->getCodebaseSourceDto()->getProjectPrefixes(),
                $this->getCodebaseSourceDto()->getCoreNamespaces(),
            );

            if ($classTransfer) {
                $results[] = $classTransfer;
            }
        }

        return $results;
    }
}
