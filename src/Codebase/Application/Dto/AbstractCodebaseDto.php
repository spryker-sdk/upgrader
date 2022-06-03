<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Dto;

use JMS\Serializer\Annotation\Type;

class AbstractCodebaseDto implements CodebaseInterface
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var \Codebase\Application\Dto\ClassCodebaseDto|null
     */
    protected ?ClassCodebaseDto $parent = null;

    /**
     * @Type("array<string>")
     *
     * @var array<string>
     */
    protected array $coreNamespaces = [];

    /**
     * @param array<string>|string $coreNamespaces
     */
    public function __construct(string $name, array $coreNamespaces = [])
    {
        $this->name = $name;
        $this->coreNamespaces = $coreNamespaces;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return \Codebase\Application\Dto\CodebaseInterface|null
     */
    public function getParent(): ?CodebaseInterface
    {
        return $this->parent;
    }

    /**
     * @return \Codebase\Application\Dto\CodebaseInterface|null
     */
    public function getCoreParent(): ?ClassCodebaseDto
    {
        $parent = $this->parent;
        while ($parent && !$parent->hasClassNameCoreNamespace()) {
            if (!$parent->getParent()) {
                break;
            }
            $parent = $parent->getParent();
        }

        return ($parent && $parent->hasClassNameCoreNamespace()) ? $parent : null;
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseInterface|null $parent
     *
     * @return \Codebase\Application\Dto\CodebaseInterface
     */
    public function setParent(?CodebaseInterface $parent): CodebaseInterface
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasClassNameCoreNamespace(): bool
    {
        foreach ($this->coreNamespaces as $coreNamespace) {
            if (strpos((string)$this->getName(), $coreNamespace) === 0) {
                return true;
            }
        }

        return false;
    }
}
