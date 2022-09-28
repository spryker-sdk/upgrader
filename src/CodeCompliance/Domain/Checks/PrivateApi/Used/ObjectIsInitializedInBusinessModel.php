<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\Filters\BusinessModelFilter;
use CodeCompliance\Domain\Checks\Filters\CoreClassFilter;
use CodeCompliance\Domain\Checks\Filters\IgnoreListFilter;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;

class ObjectIsInitializedInBusinessModel extends AbstractUsedCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:ObjectInitialization';
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
        return static::DOCUMENTATION_BASE_URL . 'private-api-is-extended.html';
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
            $classFileBody = $this->getClassFileBody($source->getReflection());
            if (!$classFileBody) {
                continue;
            }

            $createdClassNames = $this->parseCreatedClassNames($classFileBody);

            if (!$createdClassNames) {
                continue;
            }

            $useNamespaces = $this->getUseNamespaces($classFileBody);

            $createdNamespaces = $this->attachNamespaceToClassNames(
                $createdClassNames,
                $useNamespaces,
                $source->getReflection()->getNamespaceName(),
            );

            $createdSources = $this->parseSourcesByNamespaces($createdNamespaces);
            $createdSources = $this->filterService->filter($createdSources, [
                IgnoreListFilter::IGNORE_LIST_FILTER,
                CoreClassFilter::CORE_CLASS_FILTER,
            ]);

            foreach ($createdSources as $createdNamespace) {
                $guideline = sprintf($this->getGuideline(), $createdNamespace->getClassName(), $source->getClassName());
                $violations[] = new Violation((string)(new Id()), $guideline, $this->getName(), ViolationInterface::SEVERITY_ERROR, [
                    'documentation' => $this->getDocumentationUrl(),
                ]);
            }
        }

        return $violations;
    }

    /**
     * @param array<string> $namespaces
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    protected function parseSourcesByNamespaces(array $namespaces): array
    {
        $sources = [];
        foreach ($namespaces as $namespace) {
            $source = $this->codeBaseService->parsePhpClass(
                $namespace,
                $this->getCodebaseSourceDto()->getProjectPrefixes(),
                $this->getCodebaseSourceDto()->getCoreNamespaces(),
            );
            if ($source) {
                $sources[] = $source;
            }
        }

        return $sources;
    }

    /**
     * @param string $fileBody
     *
     * @return array<string>
     */
    protected function parseCreatedClassNames(string $fileBody): array
    {
        $methodNames = [];
        $matchResult = [];
        $fileBody = str_replace(';', ';' . PHP_EOL, $fileBody);
        if (!$fileBody) {
            return $methodNames;
        }
        $fileBody = preg_replace("/new\s*\n/", 'new ', $fileBody);
        if (!$fileBody) {
            return $methodNames;
        }
        preg_match_all('/new\s+((?!Transfer|Exception).)*\(*/m', $fileBody, $matchResult);
        foreach ($matchResult[0] as $row) {
            $row = preg_replace(['/new\s*/', '/^\\\\/'], '', $row);
            $pos = strpos($row, '(');

            if ($pos === false) {
                continue;
            }

            $methodNames[] = substr($row, 0, $pos);
        }

        return $methodNames;
    }

    /**
     * @param array<string> $classNames
     * @param array<string> $useNamespaces
     * @param string $defaultNamespace
     *
     * @return array<string>
     */
    protected function attachNamespaceToClassNames(array $classNames, array $useNamespaces, string $defaultNamespace): array
    {
        $results = [];

        foreach ($classNames as $name) {
            if (strpos($name, '\\') !== false) {
                $results[] = $name;

                continue;
            }

            $attachedResult = $this->attachNamespaceToClassName($name, $useNamespaces);
            if ($attachedResult) {
                $results[] = $attachedResult;

                continue;
            }

            $results[] = $defaultNamespace . $name;
        }

        return $results;
    }

    /**
     * @param string $className
     * @param array<string> $useNamespaces
     *
     * @return string|null
     */
    protected function attachNamespaceToClassName(string $className, array $useNamespaces): ?string
    {
        foreach ($useNamespaces as $namespace) {
            if (preg_match('/\/' . $className . '$/', $this->reverseSlash($namespace))) {
                return $namespace;
            }
        }

        return null;
    }

    /**
     * @param string $source
     *
     * @return string
     */
    protected function reverseSlash(string $source): string
    {
        return str_replace('\\', '/', $source);
    }

    /**
     * @param string $fileBody
     *
     * @return array<string>
     */
    protected function getUseNamespaces(string $fileBody): array
    {
        $useClasses = [];
        $matchResult = [];
        preg_match_all('/^use (.*);$/m', $fileBody, $matchResult);

        foreach ($matchResult[1] as $usageRow) {
            $pos = strpos($usageRow, ' as ');
            if ($pos) {
                $useClasses[] = substr($usageRow, 0, $pos);

                continue;
            }

            $useClasses[] = $usageRow;
        }

        return $useClasses;
    }
}
