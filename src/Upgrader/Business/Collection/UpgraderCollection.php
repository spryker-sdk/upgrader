<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Collection;

use Upgrader\Business\Exception\UpgraderException;

abstract class UpgraderCollection implements UpgraderCollectionInterface
{
    protected const ELEMENT_TYPE_MISMATCHED_TEMPLATE = 'Elements must be of type %s but %s got (%s)';

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            $this->validateElement($element);
        }
        $this->elements = $elements;
    }

    /**
     * @return string
     */
    abstract protected function getClassName(): string;

    /**
     * @param $element
     *
     * @return void
     */
    protected function validateElement($element): void
    {
        if (!$this->isValidElement($element)) {
            $this->throwInvalidObjectClassException($element);
        }
    }

    /**
     * @param $element
     *
     * @return bool
     */
    protected function isValidElement($element): bool
    {
        $className = $this->getClassName();

        return $element instanceof $className;
    }

    /**
     * @param $invalidElementData
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return void
     */
    protected function throwInvalidObjectClassException($invalidElementData): void
    {
        $errorMessage = sprintf(
            self::ELEMENT_TYPE_MISMATCHED_TEMPLATE,
            $this->getClassName(),
            gettype($invalidElementData),
            var_export($invalidElementData, true)
        );

        throw new UpgraderException($errorMessage);
    }

    /**
     * @param $element
     *
     * @return void
     */
    public function add($element): void
    {
        $this->validateElement($element);
        $this->elements[] = $element;
    }

    /**
     * @param string $key
     * @param $element
     *
     * @return void
     */
    public function set(string $key, $element): void
    {
        $this->validateElement($element);
        $this->elements[$key] = $element;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        foreach ($this as $element) {
            if (!$this->isValidElement($element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->elements = [];
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->elements[$key] ?? null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        $first = reset($this->elements);

        return $first !== false ? $first : null;
    }

    /**
     * @return mixed|null
     */
    public function last()
    {
        $end = end($this->elements);

        return $end !== false ? $end : null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @param array $elements
     *
     * @return $this
     */
    protected function createFrom(array $elements)
    {
        return new static($elements);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * @param \Upgrader\Business\Collection\UpgraderCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        foreach ($collectionToMerge->toArray() as $element) {
            $this->add($element);
        }
    }
}
