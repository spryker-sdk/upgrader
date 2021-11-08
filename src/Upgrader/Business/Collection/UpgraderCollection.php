<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Upgrader\Business\Exception\UpgraderException;

abstract class UpgraderCollection implements Countable, IteratorAggregate, ArrayAccess
{
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
     * @param mixed $element
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
     * @param mixed $element
     *
     * @return bool
     */
    protected function isValidElement($element): bool
    {
        $className = $this->getClassName();

        return $element instanceof $className;
    }

    /**
     * @param mixed $invalidElementData
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return void
     */
    protected function throwInvalidObjectClassException($invalidElementData): void
    {
        $errorMessage = sprintf(
            'Elements must be of type %s but %s got (%s)',
            $this->getClassName(),
            gettype($invalidElementData),
            var_export($invalidElementData, true),
        );

        throw new UpgraderException($errorMessage);
    }

    /**
     * @param mixed $element
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
     * @param mixed $element
     *
     * @return void
     */
    public function set(string $key, $element): void
    {
        $this->validateElement($element);
        $this->elements[$key] = $element;
    }

    /**
     * @param mixed $key
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

    /**
     * @return bool
     */
    public function valid(): bool
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
    public function rewind(): void
    {
        reset($this->elements);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]) || array_key_exists($offset, $this->elements);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (!isset($offset)) {
            $this->add($value);

            return;
        }

        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed|null|void
     */
    public function offsetUnset($offset)
    {
        if (!isset($this->elements[$offset]) && !array_key_exists($offset, $this->elements)) {
            return null;
        }

        $removed = $this->elements[$offset];
        unset($this->elements[$offset]);

        return $removed;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }
}
