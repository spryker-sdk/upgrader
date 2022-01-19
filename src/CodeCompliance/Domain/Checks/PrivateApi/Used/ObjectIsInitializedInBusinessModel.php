<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\Checks\Filters\Filters;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;

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
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $sources = $this->filterService->filter($this->getCodebaseSourceDto()->getPhpCodebaseSources(), [
            Filters::BUSINESS_MODEL_FILTER,
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

            $createdNamespaces = $this->filterCoreClasses($this->getCodebaseSourceDto()->getCoreNamespaces(), $createdNamespaces);

            foreach ($createdNamespaces as $createdNamespace) {
                $guideline = sprintf($this->getGuideline(), $createdNamespace, $source->getClassName());
                $violations[] = new Violation(new Id(), $guideline, $this->getName());
            }
        }

        return $violations;
    }

    /**
     * @param string $fileBody
     *
     * @return array
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
        preg_match_all('/new\s*((?!Transfer|Exception).)*\(*/m', $fileBody, $matchResult);
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
     * @param array $classNames
     * @param array $useNamespaces
     * @param string $defaultNamespace
     *
     * @return array
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
     * @param array $useNamespaces
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
     * @return array
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
