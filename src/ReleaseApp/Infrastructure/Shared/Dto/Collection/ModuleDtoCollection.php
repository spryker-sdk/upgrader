<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Shared\Dto\Collection;

use ReleaseApp\Application\Configuration\ReleaseAppConst;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;

class ModuleDtoCollection
{
    /**
     * @var array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    protected $elements = [];

    /**
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ModuleDto $element
     *
     * @return void
     */
    public function add(ModuleDto $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !$this->elements;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        foreach ($collectionToMerge->toArray() as $element) {
            $this->add($element);
        }
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ModuleDto|null
     */
    public function getFirstMajor(): ?ModuleDto
    {
        foreach ($this->elements as $module) {
            if ($module->getVersionType() === ReleaseAppConst::MODULE_TYPE_MAJOR) {
                return $module;
            }
        }

        return null;
    }

    /**
     * @return int
     */
    public function getMajorAmount(): int
    {
        $result = 0;
        foreach ($this->elements as $module) {
            if ($module->getVersionType() === ReleaseAppConst::MODULE_TYPE_MAJOR) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getMinorAmount(): int
    {
        $result = 0;
        foreach ($this->elements as $module) {
            if ($module->getVersionType() === ReleaseAppConst::MODULE_TYPE_MINOR) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getPathAmount(): int
    {
        $result = 0;
        foreach ($this->elements as $module) {
            if ($module->getVersionType() === ReleaseAppConst::MODULE_TYPE_PATCH) {
                $result++;
            }
        }

        return $result;
    }
}
