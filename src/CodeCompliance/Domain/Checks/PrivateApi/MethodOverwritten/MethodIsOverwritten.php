<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\PrivateApi\MethodOverwritten;

use Codebase\Application\Dto\CodebaseInterface;
use CodeCompliance\Domain\AbstractCodeComplianceCheck;
use CodeCompliance\Domain\Entity\Violation;
use Core\Domain\ValueObject\Id;
use ReflectionMethod;

class MethodIsOverwritten extends AbstractCodeComplianceCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PrivateApi:MethodIsOverwritten';
    }

    /**
     * @return string
     */
    public function getGuideline(): string
    {
        return 'Please avoid usage of core method %s::%s() in the class %s';
    }

    /**
     * @var array<string>
     */
    protected const PATTERNS_TO_FILTER = ['DependencyProvider', 'EntityManager', 'Repository', 'Factory'];

    /**
     * @return array<\CodeCompliance\Domain\Entity\Violation>
     */
    public function getViolations(): array
    {
        $violations = [];

        foreach (static::PATTERNS_TO_FILTER as $pattern) {
            $filteredSources = $this->filterProjectClassesWithExtendedCoreByPattern($this->getCodebaseSourceDto()->getPhpCodebaseSources(), $pattern);

            foreach ($filteredSources as $filteredSource) {
                if ($filteredSource->getCoreParent() === null) {
                    continue;
                }
                foreach ($this->filterNotUniqueMethods($filteredSource) as $filteredMethod) {
                    $guideline = sprintf($this->getGuideline(), $filteredSource->getCoreParent()->getClassName(), $filteredMethod->getName(), $filteredSource->getClassName());
                    $violations[] = new Violation(new Id(), $guideline, $this->getName());
                }
            }
        }

        return $violations;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     * @param string $pattern
     *
     * @return array
     */
    public function filterProjectClassesWithExtendedCoreByPattern(array $sources, string $pattern): array
    {
        return array_filter($sources, function (CodebaseInterface $transfer) use ($pattern) {
            $coreParent = $transfer->getCoreParent();

            return ($coreParent && $this->isRequiredClass((string)$coreParent->getClassName(), $pattern));
        });
    }

    /**
     * @param string $className
     * @param string $pattern
     *
     * @return bool
     */
    protected function isRequiredClass(string $className, string $pattern): bool
    {
        return (bool)preg_match(sprintf('/%s$/', $pattern), $className);
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseInterface $source
     *
     * @return array
     */
    public function filterNotUniqueMethods(CodebaseInterface $source): array
    {
        $coreMethods = [];
        foreach ($source->getCoreMethods() as $coreMethod) {
            $coreMethods[$coreMethod->getName()] = $coreMethod;
        }

        return array_filter($source->getProjectMethods(), function ($method) use ($coreMethods, $source) {
            /** @var \ReflectionMethod $method */
            $isMethodNotUnique = array_key_exists($method->getName(), $coreMethods);
            $isNotPlugin = strpos($method->getName(), 'Plugin') === false;
            $isPluginByDocComment = $this->isPluginReturnInDocComment((string)$method->getDocComment());
            $isPluginCreatedInContext = $this->isPluginCreatedInContext($this->getMethodBody($method));
            $isProvidedDependency = preg_match('/^provide.*Dependencies$/', $method->getName());
            $isReturnEmptyArrayInContext = $isMethodNotUnique && $this->isReturnEmptyArrayInContext(
                $this->getMethodBody($coreMethods[$method->getName()]),
            );

            return (
                !$isProvidedDependency &&
                $source->getCoreParent() &&
                $isMethodNotUnique &&
                $isNotPlugin &&
                !$isPluginByDocComment &&
                !$isPluginCreatedInContext &&
                !$isReturnEmptyArrayInContext
            );
        });
    }

    /**
     * @param string $context
     *
     * @return bool
     */
    protected function isPluginCreatedInContext(string $context): bool
    {
        return (bool)preg_match('/(new){1}(.)*(Plugin|Console|EventSubscriber)\(\)/m', $context);
    }

    /**
     * @param string $context
     *
     * @return bool
     */
    protected function isReturnEmptyArrayInContext(string $context): bool
    {
        return (bool)preg_match('/((return)(.)*\[])|((return)(.)*array\(\))/m', $context);
    }

    /**
     * @param string $docComment
     *
     * @return bool
     */
    protected function isPluginReturnInDocComment(string $docComment): bool
    {
        return (bool)preg_match('/.*@return.*PluginInterface.*/m', $docComment);
    }

    /**
     * @param \ReflectionMethod $method
     *
     * @return string
     */
    protected function getMethodBody(ReflectionMethod $method): string
    {
        $file = $method->getFileName();
        $startLine = $method->getStartLine() - 1;
        $endLine = $method->getEndLine();

        if ($file) {
            $source = file_get_contents($file);

            if ($source) {
                $source = preg_split('/' . PHP_EOL . '/', $source);

                if ($source) {
                    $body = '';

                    for ($i = $startLine; $i < $endLine; $i++) {
                        $body .= $source[$i] . PHP_EOL;
                    }

                    return $body;
                }
            }
        }

        return '';
    }
}
