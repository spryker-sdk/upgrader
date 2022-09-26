<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\Filters\BusinessModelFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class PersistenceInBusinessModel extends AbstractUsedCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:PersistenceInBusinessModel';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Please avoid usage of PrivateApi %s->%s(...) in %s';
    }

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $codebaseSourceDto = $this->getCodebaseSourceDto();
        $sources = $this->filterService->filter($codebaseSourceDto->getPhpCodebaseSources(), [
            BusinessModelFilter::BUSINESS_MODEL_FILTER,
        ]);
        $violations = [];

        foreach ($sources as $source) {
            $reflectionMethod = $this->getMethod($source->getReflection(), '__construct');
            if (!$reflectionMethod) {
                continue;
            }
            $constructorDoc = $reflectionMethod->getDocComment();
            if (!$constructorDoc) {
                continue;
            }
            $constructorArgumentNamespaces = $this->getParamNamespacesFromDocComment($constructorDoc);
            $repositoryAndEntityManagerNamespaces = $this->filterRepositoryAndEntityManager($constructorArgumentNamespaces);

            if (!count($repositoryAndEntityManagerNamespaces)) {
                continue;
            }

            foreach ($repositoryAndEntityManagerNamespaces as $classNamespace) {
                $property = $this->getPropertyByNamespace($source->getReflection(), $classNamespace);
                if (!$property) {
                    continue;
                }
                $classFileBody = $this->getClassFileBody($source->getReflection());
                if (!$classFileBody) {
                    continue;
                }
                $methodNames = $this->parseUsedMethodsFromProperty($classFileBody, $property->getName());
                $classTransfer = $this->codeBaseService->parsePhpClass(
                    ltrim($classNamespace, '\\'),
                    $codebaseSourceDto->getProjectPrefixes(),
                    $codebaseSourceDto->getCoreNamespaces(),
                );
                if ($classTransfer === null) {
                    continue;
                }

                foreach ($methodNames as $methodName) {
                    $methodReflection = $classTransfer->getReflection()->getMethod($methodName);
                    if (
                        $this->hasCoreNamespace(
                            $codebaseSourceDto->getCoreNamespaces(),
                            ltrim($methodReflection->getDeclaringClass()->getName(), '\\'),
                        )
                    ) {
                        $guideline = sprintf($this->getGuideline(), $methodReflection->getDeclaringClass()->getName(), $methodName, $source->getClassName());
                        $violations[] = new Violation((string)(new Id()), $guideline, $this->getName());
                    }
                }
            }
        }

        return $violations;
    }

    /**
     * @param string $classNamespace
     *
     * @return bool
     */
    protected function isRepositoryOrEntityManager(string $classNamespace): bool
    {
        $patternList = [
            '/\w+EntityManager$/',
            '/\w+EntityManagerInterface$/',
            '/\w+Repository$/',
            '/\w+RepositoryInterface$/',
        ];

        foreach ($patternList as $pattern) {
            if (preg_match($pattern, $classNamespace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string> $classNamespaceList
     *
     * @return array<string>
     */
    protected function filterRepositoryAndEntityManager(array $classNamespaceList): array
    {
        return array_filter($classNamespaceList, function (string $classNamespace) {
            return $this->isRepositoryOrEntityManager($classNamespace);
        });
    }

    /**
     * @param string $comment
     *
     * @return string
     */
    protected function getVarNamespaceFromDocComment(string $comment): string
    {
        $matchResult = [];
        preg_match('/\* @var (.*)/m', $comment, $matchResult);
        $namespace = $matchResult[1];

        $pos = strpos($namespace, ' $');
        if ($pos) {
            $namespace = substr($namespace, 0, $pos);
        }

        return $namespace;
    }

    /**
     * @param string $fileBody
     * @param string $propertyName
     *
     * @return array<string>
     */
    protected function parseUsedMethodsFromProperty(string $fileBody, string $propertyName): array
    {
        $methodNames = [];
        $matchResult = [];
        $fileBody = preg_replace("/(?<=\w)\n/", '', $fileBody);
        if (!$fileBody) {
            return $methodNames;
        }
        preg_match_all('/\$this[ ]*->' . $propertyName . '[ ]*->(.*)\(.*\)/m', $fileBody, $matchResult);
        foreach ($matchResult[1] as $row) {
            $pos = strpos($row, '(');
            if ($pos) {
                $methodNames[] = substr($row, 0, $pos);

                continue;
            }

            $methodNames[] = $row;
        }

        return $methodNames;
    }

    /**
     * @phpstan-template T of object
     *
     * @param \ReflectionClass<T> $class
     * @param string $namespace
     *
     * @return \ReflectionProperty|null
     */
    protected function getPropertyByNamespace(ReflectionClass $class, string $namespace): ?ReflectionProperty
    {
        foreach ($class->getProperties() as $property) {
            $comment = $property->getDocComment();
            if ($comment && $this->getVarNamespaceFromDocComment($comment) === $namespace) {
                return $property;
            }
        }

        return null;
    }

    /**
     * @phpstan-template T of object
     *
     * @param \ReflectionClass<T> $reflectionClass
     * @param string $methodName
     *
     * @return \ReflectionMethod|null
     */
    protected function getMethod(ReflectionClass $reflectionClass, string $methodName): ?ReflectionMethod
    {
        try {
            $reflectionMethod = $reflectionClass->getMethod($methodName);
        } catch (Exception $exception) {
            $reflectionMethod = null;
        }

        return $reflectionMethod ?? null;
    }
}
