<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\Mapper;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Application\Dto\MethodCodebaseDto;
use ReflectionClass;

class ReflectionClassToClassCodebaseDtoMapper implements ReflectionClassToClassCodebaseDtoMapperInterface
{
    /**
     * @param \ReflectionClass $reflectionClass
     * @param array<string> $projectPrefixes
     * @param array<string> $coreNamespaces
     *
     * @return \Codebase\Application\Dto\ClassCodebaseDto
     */
    public function map(
        ReflectionClass $reflectionClass,
        array $projectPrefixes,
        array $coreNamespaces = [],
        int $iteration = 0
    ): ClassCodebaseDto {
        if (!isset($GLOBALS['sources'])) {
            $GLOBALS['sources'] = [];
        }

        if (isset($GLOBALS['sources'][$reflectionClass->getName()])) {
            return $GLOBALS['sources'][$reflectionClass->getName()];
        } else {
            $transfer = new ClassCodebaseDto(
                $reflectionClass->getName(),
                $reflectionClass->getShortName(),
                $reflectionClass->getNamespaceName(),
                $coreNamespaces,
            );
            $GLOBALS['sources'][$transfer->getName()] = $transfer;
        }


        $transfer->setConstants($reflectionClass->getConstants());
        $transfer->setExtendCore($this->isExtendCore($reflectionClass, $projectPrefixes, $coreNamespaces));

        if($iteration < 5) {

//            foreach ($reflectionClass->getTraits() as $trait) {
//                $transfer->addTrait($this->map($trait, $projectPrefixes, $coreNamespaces, $iteration + 1));
//            }

            $transfer->setMethods(
                $this->getMethodCodebaseDtos($iteration + 1, $reflectionClass->getMethods(), $transfer, $projectPrefixes, $coreNamespaces),
            );
        }

//        $coreInterfacesMethods = $this->getCoreInterfacesMethods($reflectionClass->getInterfaces(), $projectPrefixes);
//        $transfer->setCoreInterfacesMethods(
//            $this->getMethodCodebaseDtos($coreInterfacesMethods, $transfer, $projectPrefixes, $coreNamespaces)
//        );
//
//        if ($coreNamespaces !== []) {
//            $projectMethods = $this->getProjectMethods($reflectionClass->getName(), $reflectionClass->getMethods(), $coreNamespaces);
//            $transfer->setProjectMethods($this->getMethodCodebaseDtos($projectMethods, $transfer, $projectPrefixes, $coreNamespaces));
//        }
//
//        $parentClass = $reflectionClass->getParentClass();
//        if ($parentClass) {
//            if ($coreNamespaces !== []) {
//                $coreMethods = $this->getCoreMethods($parentClass->getMethods(), $coreNamespaces);
//                $transfer->setCoreMethods($this->getMethodCodebaseDtos($coreMethods, $transfer, $projectPrefixes, $coreNamespaces));
//            }
//        }

        return $transfer;
    }

    /**
     * @param array $reflectionMethods
     * @param array $projectPrefixes
     * @param array $coreNamespaces
     *
     * @return array
     */
    protected function getMethodCodebaseDtos(
        int $iteration,
        array $reflectionMethods,
        ClassCodebaseDto $defaultDeclarationClass,
        array $projectPrefixes,
        array $coreNamespaces = [],
    ): array {
        $list = [];
        foreach ($reflectionMethods as $reflectionMethod) {
            $methodCodebaseDto = new MethodCodebaseDto($reflectionMethod->getName(), $defaultDeclarationClass);
            if ($reflectionMethod->getDeclaringClass()->getName() !== $defaultDeclarationClass->getName()) {
                $declaringClass = $this->map($reflectionMethod->getDeclaringClass(), $projectPrefixes, $coreNamespaces, $iteration);
                $methodCodebaseDto->setDeclaringClass($declaringClass);
            }
            $list[] = $methodCodebaseDto;
        }

        return $list;
    }

    /**
     * @param string $projectClassName
     * @param array<\ReflectionMethod> $methods
     * @param array<string> $coreNamespaces
     *
     * @return array<\ReflectionMethod>
     */
    protected function getProjectMethods(string $projectClassName, array $methods, array $coreNamespaces): array
    {
        return array_filter($methods, function ($method) use ($projectClassName, $coreNamespaces) {
            foreach ($coreNamespaces as $coreNamespace) {
                $isProjectClassMethod = $method->getDeclaringClass()->getName() == $projectClassName;
                $hasNoCoreNamespace = strpos($method->getDeclaringClass()->getNamespaceName(), $coreNamespace) !== 0;
                if ($isProjectClassMethod && $hasNoCoreNamespace) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @param \ReflectionClass<\Codebase\Infrastructure\SourceParser\Mapper\T> $projectClass
     * @param array<string> $projectPrefix
     * @param array<string> $coreNamespaces
     *
     * @return bool
     */
    protected function isExtendCore(ReflectionClass $projectClass, array $projectPrefix, array $coreNamespaces): bool
    {
        if ($coreNamespaces === []) {
            return false;
        }

        $parentClass = $projectClass->getParentClass();
        if ($parentClass) {
            $parentMethods = $this->getCoreMethods($parentClass->getMethods(), $coreNamespaces);
            if (count($parentMethods)) {
                return true;
            }
        }

        $interfacesMethods = $this->getCoreInterfacesMethods($projectClass->getInterfaces(), $projectPrefix);
        $parentMethods = $this->getCoreMethods($interfacesMethods, $coreNamespaces);
        if (count($parentMethods)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<\ReflectionMethod> $methods
     * @param array<string> $coreNamespaces
     *
     * @return array<\ReflectionMethod>
     */
    protected function getCoreMethods(array $methods, array $coreNamespaces): array
    {
        return array_filter($methods, function ($method) use ($coreNamespaces) {
            foreach ($coreNamespaces as $coreNamespace) {
                if (strpos($method->getDeclaringClass()->getNamespaceName(), $coreNamespace) === 0) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @param array<\ReflectionClass<\Codebase\Infrastructure\SourceParser\Mapper\T>> $interfaces
     * @param array<string> $projectPrefixes
     *
     * @return array<\ReflectionMethod>
     */
    protected function getCoreInterfacesMethods(array $interfaces, array $projectPrefixes): array
    {
        $methods = [];

        $coreInterfaces = array_filter($interfaces, function ($interface) use ($projectPrefixes) {
            foreach ($projectPrefixes as $projectPrefix) {
                if (strpos($interface->getNamespaceName(), $projectPrefix) === 0) {
                    return false;
                }
            }

            return true;
        });

        foreach ($coreInterfaces as $interface) {
            $methods = array_merge($methods, $interface->getMethods());
        }

        return $methods;
    }
}
