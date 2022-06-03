<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

use JMS\Serializer\Annotation\Type;

class ClassCodebaseDto extends AbstractCodebaseDto
{
    /**
     * @var string
     */
    protected string $shortName;

    /**
     * @var string
     */
    protected string $namespaceName;

    /**
     * @var bool
     */
    protected bool $extendsCore = false;

    /**
     * @var string|null
     */
    protected ?string $fileName = null;

    /**
     * @var int|null
     */
    protected ?int $endLine = null;

    /**
     * @var string|null
     */
    protected ?string $docComment = null;

    /**
     * @var \Codebase\Application\Dto\ClassCodebaseDto|null
     */
    protected ?ClassCodebaseDto $parentClass = null;

    /**
     * @Type("array")
     *
     * @var array<string, string>
     */
    protected array $constants = [];

    /**
     * @Type("array<Codebase\Application\Dto\MethodCodebaseDto>")
     *
     * @var array<\Codebase\Application\Dto\MethodCodebaseDto>
     */
    protected array $methods = [];
//
//    /**
//     * @Type("array<Codebase\Application\Dto\ClassCodebaseDto>")
//     *
//     * @var array<self>
//     */
//    protected array $traits = [];
//
//    /**
//     * @Type("array<Codebase\Application\Dto\MethodCodebaseDto>")
//     *
//     * @var array<\Codebase\Application\Dto\MethodCodebaseDto>
//     */
//    protected array $projectMethods = [];
//
//    /**
//     * @Type("array<Codebase\Application\Dto\MethodCodebaseDto>")
//     *
//     * @var array<\Codebase\Application\Dto\MethodCodebaseDto>
//     */
//    protected array $coreMethods = [];
//
//    /**
//     * @Type("array<Codebase\Application\Dto\MethodCodebaseDto>")
//     *
//     * @var array<\Codebase\Application\Dto\MethodCodebaseDto>
//     */
//    protected array $coreInterfacesMethods = [];
//
//    /**
//     * @Type("array<Codebase\Application\Dto\PropertyCodebaseDto>")
//     *
//     * @var array<\Codebase\Application\Dto\PropertyCodebaseDto>
//     */
//    protected array $properties = [];

    /**
     * @param string $name
     * @param string $shortName
     * @param string $namespaceName
     * @param array $coreNamespaces
     */
    public function __construct(
        string $name,
        string $shortName,
        string $namespaceName,
        array $coreNamespaces = []
    ) {
        parent::__construct($name, $coreNamespaces);
        $this->shortName = $shortName;
        $this->namespaceName = $namespaceName;
    }

    /**
     * @return bool
     */
    public function isExtendsCore(): bool
    {
        return $this->extendsCore;
    }

    /**
     * @return string
     */
    public function getNamespaceName(): string
    {
        return $this->namespaceName;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return int|null
     */
    public function getEndLine(): ?int
    {
        return $this->endLine;
    }

    /**
     * @param int|null $endLine
     *
     * @return void
     */
    public function setEndLine(?int $endLine): void
    {
        $this->endLine = $endLine;
    }

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
     * @return array<\Codebase\Application\Dto\MethodCodebaseDto>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array<\Codebase\Application\Dto\MethodCodebaseDto> $method
     *
     * @return void
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @return array<self>
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @param self $trait
     *
     * @return void
     */
    public function addTrait(self $trait): void
    {
        $this->traits[] = $trait;
    }

    /**
     * @return array<\Codebase\Application\Dto\MethodCodebaseDto>
     */
    public function getProjectMethods(): array
    {
        return $this->projectMethods;
    }

    /**
     * @param array<\Codebase\Application\Dto\ClassCodebaseDto> $projectMethods
     *
     * @return void
     */
    public function setProjectMethods(array $projectMethods): void
    {
        $this->projectMethods = $projectMethods;
    }

    /**
     * @return array<\Codebase\Application\Dto\MethodCodebaseDto>
     */
    public function getCoreMethods(): array
    {
        return $this->coreMethods;
    }

    /**
     * @param array<\Codebase\Application\Dto\MethodCodebaseDto> $coreMethods
     *
     * @return void
     */
    public function setCoreMethods(array $coreMethods): void
    {
        $this->coreMethods = $coreMethods;
    }

    /**
     * @return array<\Codebase\Application\Dto\MethodCodebaseDto>
     */
    public function getCoreInterfacesMethods(): array
    {
        return $this->coreInterfacesMethods;
    }

    /**
     * @param array<\Codebase\Application\Dto\MethodCodebaseDto> $coreInterfacesMethod
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

    /**
     * @return \Codebase\Application\Dto\ClassCodebaseDto|null
     */
    public function getParentClass(): ?ClassCodebaseDto
    {
        return $this->parentClass;
    }

    /**
     * @param \Codebase\Application\Dto\ClassCodebaseDto|null $parentClass
     *
     * @return void
     */
    public function setParentClass(?ClassCodebaseDto $parentClass): void
    {
        $this->parentClass = $parentClass;
    }

    /**
     * @return string|null
     */
    public function getDocComment(): ?string
    {
        return $this->docComment;
    }

    /**
     * @param string|null $docComment
     *
     * @return void
     */
    public function setDocComment(?string $docComment): void
    {
        $this->docComment = $docComment;
    }

    /**
     * @return array<\Codebase\Application\Dto\PropertyCodebaseDto>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getMethod(string $methodName): ?MethodCodebaseDto
    {
        return null;
    }
}
