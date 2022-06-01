<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

use JMS\Serializer\Annotation\Type;
use ReflectionClass;

/**
 * @phpstan-template T of object
 */
class ClassCodebaseDto extends AbstractCodebaseDto
{
    /**
     * @var bool
     */
    protected bool $extendsCore = false;

    /**
     * @Type("array<string>")
     *
     * @var array<int, string>
     */
    protected array $constants = [];

    /**
     * @Type("array<string>")
     *
     * @var array<string>
     */
    protected array $methods = [];

    /**
     * @Type("array<string>")
     *
     * @var array<string>
     */
    protected array $traits = [];

    /**
     * @var \ReflectionClass<T>
     */
    protected ?ReflectionClass $reflection = null;

    /**
     * @Type("array<string>")
     *
     * @var array<\ReflectionMethod>
     */
    protected array $projectMethods = [];

    /**
     * @Type("array<string>")
     *
     * @var array<\ReflectionMethod>
     */
    protected array $coreMethods = [];

    /**
     * @Type("array<string>")
     *
     * @var array<\ReflectionMethod>
     */
    protected array $coreInterfacesMethods = [];

    /**
     * @return array<int, string>
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * @param array<string> $constants
     *
     * @return $this
     */
    public function setConstants(array $constants)
    {
        $this->constants = $constants;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array<string> $methods
     *
     * @return $this
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @param array<string> $traits
     *
     * @return $this
     */
    public function setTraits(array $traits)
    {
        $this->traits = $traits;

        return $this;
    }

    /**
     * @return \ReflectionClass<T>
     */
    public function getReflection(): ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @param \ReflectionClass<T> $reflection
     *
     * @return $this
     */
    public function setReflection(ReflectionClass $reflection)
    {
        $this->reflection = $reflection;

        return $this;
    }

    /**
     * @return array<\ReflectionMethod>
     */
    public function getProjectMethods(): array
    {
        return $this->projectMethods;
    }

    /**
     * @param array<\ReflectionMethod> $projectMethods
     *
     * @return void
     */
    public function setProjectMethods(array $projectMethods): void
    {
        $this->projectMethods = $projectMethods;
    }

    /**
     * @return array<\ReflectionMethod>
     */
    public function getCoreMethods(): array
    {
        return $this->coreMethods;
    }

    /**
     * @param array<\ReflectionMethod> $coreMethods
     *
     * @return void
     */
    public function setCoreMethods(array $coreMethods): void
    {
        $this->coreMethods = $coreMethods;
    }

    /**
     * @return array<\ReflectionMethod>
     */
    public function getCoreInterfacesMethods(): array
    {
        return $this->coreInterfacesMethods;
    }

    /**
     * @param array<\ReflectionMethod> $coreInterfacesMethods
     *
     * @return void
     */
    public function setCoreInterfacesMethods(array $coreInterfacesMethods): void
    {
        $this->coreInterfacesMethods = $coreInterfacesMethods;
    }

    /**
     * @return bool
     */
    public function isExtendCore(): bool
    {
        return $this->extendsCore;
    }

    /**
     * @param bool $extendsCore
     *
     * @return void
     */
    public function setExtendCore(bool $extendsCore): void
    {
        $this->extendsCore = $extendsCore;
    }
}
