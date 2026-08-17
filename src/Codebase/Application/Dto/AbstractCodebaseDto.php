<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Application\Dto;

class AbstractCodebaseDto implements CodebaseInterface
{
    /**
     * @var string|null
     */
    protected $className;

    /**
     * @var \Codebase\Application\Dto\CodebaseInterface|null
     */
    protected $parent;

    /**
     * @var array<string>
     */
    protected array $coreNamespaces = [];

    /**
     * @param array<string> $coreNamespaces
     */
    public function __construct(array $coreNamespaces = [])
    {
        $this->coreNamespaces = $coreNamespaces;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function setClassName(?string $className = null): CodebaseInterface
    {
        $this->className = $className;

        return $this;
    }

    public function getParent(): ?CodebaseInterface
    {
        return $this->parent;
    }

    public function getCoreParent(): ?CodebaseInterface
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

    public function setParent(?CodebaseInterface $parent): CodebaseInterface
    {
        $this->parent = $parent;

        return $this;
    }

    public function hasClassNameCoreNamespace(): bool
    {
        foreach ($this->coreNamespaces as $coreNamespace) {
            if (strpos((string)$this->getClassName(), $coreNamespace) === 0) {
                return true;
            }
        }

        return false;
    }
}
