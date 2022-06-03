<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Application\Dto\MethodCodebaseDto;
use Codebase\Application\Dto\PropertyCodebaseDto;
use CodeCompliance\Domain\Checks\Filters\BusinessModelFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use Exception;

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
        $sources = $this->filterService->filter($this->getCodebaseSourceDto()->getPhpCodebaseSources(), [
            BusinessModelFilter::BUSINESS_MODEL_FILTER,
        ]);
        $violations = [];

        foreach ($sources as $source) {
            $methodCodebaseDto = $this->getMethod($source, '__construct');
            if (!$methodCodebaseDto) {
                continue;
            }
            $constructorDoc = $methodCodebaseDto->getDocComment();
            if (!$constructorDoc) {
                continue;
            }
            $constructorArgumentNamespaces = $this->getParamNamespacesFromDocComment($constructorDoc);
            $repositoryAndEntityManagerNamespaces = $this->filterRepositoryAndEntityManager($constructorArgumentNamespaces);

            if (!count($repositoryAndEntityManagerNamespaces)) {
                continue;
            }

            foreach ($repositoryAndEntityManagerNamespaces as $classNamespace) {
                $property = $this->getPropertyByNamespace($source, $classNamespace);
                if (!$property) {
                    continue;
                }
                $classFileBody = $this->getClassFileBody($source);
                if (!$classFileBody) {
                    continue;
                }
                $methodNames = $this->parseUsedMethodsFromProperty($classFileBody, $property->getName());
                $classNamespace = ltrim($classNamespace, '\\');
                /** @var \Codebase\Application\Dto\ClassCodebaseDto $classTransfer */
                $classTransfer = $this->getCodebaseSourceDto()->getPhpCodebaseSources()[$classNamespace] ??
                    $this->getCodebaseSourceDto()->getPhpCoreCodebaseSources()[$classNamespace] ?? null;

                if ($classTransfer === null) {
                    continue;
                }

                foreach ($methodNames as $methodName) {
                    $methodReflection = $classTransfer->getMethod($methodName);
                    if (
                        $this->hasCoreNamespace(
                            $this->getCodebaseSourceDto()->getCoreNamespaces(),
                            ltrim($methodReflection->getDeclaringClass()->getName(), '\\'),
                        )
                    ) {
                        $guideline = sprintf($this->getGuideline(), $methodReflection->getDeclaringClass()->getName(), $methodName, $source->getName());
                        $violations[] = new Violation(new Id(), $guideline, $this->getName());
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
     * @param \Codebase\Application\Dto\ClassCodebaseDto $classCodebaseDto
     * @param string $namespace
     *
     * @return \Codebase\Application\Dto\PropertyCodebaseDto|null
     */
    protected function getPropertyByNamespace(ClassCodebaseDto $classCodebaseDto, string $namespace): ?PropertyCodebaseDto
    {
        foreach ($classCodebaseDto->getProperties() as $property) {
            $comment = $property->getDocComment();
            if ($comment && $this->getVarNamespaceFromDocComment($comment) === $namespace) {
                return $property;
            }
        }

        return null;
    }

    /**
     * @param \Codebase\Application\Dto\ClassCodebaseDto $classCodebaseDto
     * @param string $methodName
     *
     * @return \Codebase\Application\Dto\MethodCodebaseDto|null
     */
    protected function getMethod(ClassCodebaseDto $classCodebaseDto, string $methodName): ?MethodCodebaseDto
    {
        try {
            $methodCodebaseDto = $classCodebaseDto->getMethod($methodName);
        } catch (Exception $exception) {
            $methodCodebaseDto = null;
        }

        return $methodCodebaseDto ?? null;
    }
}
