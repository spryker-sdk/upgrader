<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain\Resolves\NotUnique;

use CodeCompliance\Domain\Checks\Filters\IgnoreListParentFilter;
use CodeCompliance\Domain\Service\FilterService;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Resolve\Domain\AbstractResolveCheck;
use Resolve\Domain\Entity\Message;
use CodeCompliance\Domain\Checks\Filters\PluginFilter;
use ReflectionClass;
use Resolve\Domain\Service\CodeBaseServiceInterface;

class Method extends AbstractResolveCheck
{
    /**
     * @var string
     */
    public const RECTOR_FILE_PATH = '/rector/methods_to_replace.json';

    /**
     * @var string
     */
    public const RECTOR_DIR = 'rector';


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
    public function getResult(): string
    {

        // TODO: Re-use the CodeComplinace code partially here to grab all violation with same rules

        $sources = $this->getCodebaseSourceDto()->getPhpCodebaseSources();
        $filteredSources = $this->filterService->filter($sources, [
            PluginFilter::PLUGIN_FILTER, IgnoreListParentFilter::IGNORE_LIST_PARENT_FILTER,
        ]);

        $violations = [];

        foreach ($filteredSources as $source) {
            $namesCoreMethods = array_column($source->getCoreMethods(), static::COLUMN_KEY_NAME);
            $nameCoreInterfaceMethods = array_column($source->getCoreInterfacesMethods(), static::COLUMN_KEY_NAME);
            $projectPrefixes = $this->getCodebaseSourceDto()->getProjectPrefixes();

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
                if ($this->isPluginReturnInDocComment((string)$projectMethod->getDocComment())) {
                    continue;
                }

                $isCoreMethod = in_array($projectMethod->getName(), $namesCoreMethods);
                $isMethodDeclaredInInterface = in_array($projectMethod->getName(), $nameCoreInterfaceMethods);
                $hasProjectPrefix = $this->hasProjectPrefix($projectMethod->getName(), $projectPrefixes);

                if ($source->isExtendCore() && !$isCoreMethod && !$hasProjectPrefix && !$isMethodDeclaredInInterface) {
                    $methodParts = preg_split('/(?=[A-Z])/', $projectMethod->getName()) ?: [];
                    array_splice($methodParts, 1, 0, [reset($projectPrefixes)]);

                    $violations[] = [
                        'filePath' => $projectMethod->getFileName(),
                        'className' => $source->getClassName(),
                        'methodName' => $projectMethod->getName(),
                        'desiredMethodName' => lcfirst(implode('', $methodParts))
                    ];
                }
            }
        }

        // when do not find any violations
        if (count($violations)<=0) {
            return $this->getName() . ':' . 'Already Resolved';
        }

        if (!is_dir(static::RECTOR_DIR)) {
            mkdir(static::RECTOR_DIR, 0777, true);
        }
        if (!file_exists(getcwd() . static::RECTOR_FILE_PATH)) {
            touch(getcwd() . static::RECTOR_FILE_PATH);
        }
        file_put_contents(getcwd() . static::RECTOR_FILE_PATH, json_encode($violations));

        return $this->getName() . ':' . 'Prepared.';
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
     * @phpstan-template T of object
     *
     * @phpstan-param array<\ReflectionClass<T>> $interfaces
     *
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
     * @phpstan-template T of object
     *
     * @param string $method
     * @param \ReflectionClass<T> $class
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

    /**
     * @param string $value
     * @param array<string> $projectPrefixes
     *
     * @return bool
     */
    protected function hasProjectPrefix(string $value, array $projectPrefixes): bool
    {
        foreach ($projectPrefixes as $projectPrefix) {
            if (strpos($value, $projectPrefix) !== false) {
                return true;
            }
        }

        return false;
    }
}
