<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;
use JMS\Serializer\Annotation\Type;

/**
 * @phpstan-template T of object
 */
class MethodCodebaseDto
{
    /**
     * @Type("string")
     *
     * @var string
     */
    protected string $name;

    /**
     * @Type("Codebase\Application\Dto\ClassCodebaseDto|null")
     *
     * @var \Codebase\Application\Dto\ClassCodebaseDto|null
     */
    protected ?ClassCodebaseDto $declaringClass;

    /**
     * @param string $name
     * @param ClassCodebaseDto $declaringClass
     */
    public function __construct(string $name, ?ClassCodebaseDto $declaringClass = null)
    {
        $this->name = $name;
        $this->declaringClass = $declaringClass;
    }

    /**
     * @param \Codebase\Application\Dto\ClassCodebaseDto $declaringClass
     *
     * @return void
     */
    public function setDeclaringClass(ClassCodebaseDto $declaringClass): void
    {
        $this->declaringClass = $declaringClass;
    }

    /**
     * @return \Codebase\Application\Dto\ClassCodebaseDto
     */
    public function getDeclaringClass(): ClassCodebaseDto
    {
        return $this->declaringClass;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
