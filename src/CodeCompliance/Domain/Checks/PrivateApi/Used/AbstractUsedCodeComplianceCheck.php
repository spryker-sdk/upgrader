<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\PrivateApi\Used;

use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use ReflectionClass;
use ReflectionMethod;

abstract class AbstractUsedCodeComplianceCheck extends AbstractCodeComplianceCheck
{
    /**
     * @phpstan-template T of object
     *
     * @param \ReflectionClass<T> $class
     *
     * @return string|null
     */
    protected function getClassFileBody(ReflectionClass $class): ?string
    {
        $filename = $class->getFileName();

        if (!$filename) {
            return null;
        }

        $sourceFile = file($filename);

        if (!$sourceFile) {
            return null;
        }

        $startLine = 0;
        $endLine = $class->getEndLine();
        $length = $endLine - $startLine;

        return implode('', array_slice($sourceFile, $startLine, $length));
    }

    /**
     * @param string $fileBody
     * @param string $getterName
     *
     * @return array<string>
     */
    protected function parseUsedMethodsFromGetter(string $fileBody, string $getterName): array
    {
        $methodNames = [];
        $matchResult = [];
        $fileBody = preg_replace("/(?<=\w|\))\n/", '', $fileBody);
        if (!$fileBody) {
            return $methodNames;
        }
        preg_match_all('/\$this[ ]*->' . $getterName . '\(\)[ ]*->(.*)\(.*\)/m', $fileBody, $matchResult);
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
     * @param string $fileBody
     *
     * @return array<string>
     */
    protected function parseUsedMethods(string $fileBody): array
    {
        $methodNames = [];
        $matchResult = [];
        $fileBody = preg_replace("/(?<=\w)\n/", '', $fileBody);
        if (!$fileBody) {
            return $methodNames;
        }
        preg_match_all('/\$this[ ]*->([a-zA-Z]*)\(.*\)/m', $fileBody, $matchResult);
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
     * @param string $comment
     * @param string $method
     *
     * @return string|null
     */
    protected function getReturnNamespaceByMethodFromDocComment(string $comment, string $method): ?string
    {
        $matchResult = [];
        preg_match('/\* @method (.*) ' . $method . '\(\)/', $comment, $matchResult);

        if (!isset($matchResult[1])) {
            return null;
        }

        return substr($matchResult[1], 1);
    }

    /**
     * @param \ReflectionMethod $method
     *
     * @return string|null
     */
    protected function getMethodBody(ReflectionMethod $method): ?string
    {
        $filename = $method->getFileName();

        if (!$filename) {
            return null;
        }

        $sourceFile = file($filename);

        if (!$sourceFile) {
            return null;
        }

        $startLine = $method->getStartLine() - 1;
        $endLine = $method->getEndLine();
        $length = $endLine - $startLine;

        return implode('', array_slice($sourceFile, $startLine, $length));
    }

    /**
     * @param array<string> $coreNamespaces
     * @param array<string> $classNamespaceList
     *
     * @return array<string>
     */
    protected function filterCoreClasses(array $coreNamespaces, array $classNamespaceList): array
    {
        return array_filter($classNamespaceList, function ($classNamespace) use ($coreNamespaces) {
            return $this->hasCoreNamespace($coreNamespaces, $classNamespace);
        });
    }

    /**
     * @param string $comment
     *
     * @return array<string>
     */
    public function getParamNamespacesFromDocComment(string $comment): array
    {
        $matchResult = [];
        preg_match_all('/\* @param (.*) \$/m', $comment, $matchResult);

        $results = [];
        foreach ($matchResult[1] as $namespace) {
//            if (substr($namespace, 0, 1) === '\\') {
//                $results[] = substr($namespace, 1);
//
//                continue;
//            }
            $results[] = $namespace;
        }

        return $results;
    }
}
