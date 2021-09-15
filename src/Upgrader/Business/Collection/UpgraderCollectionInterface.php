<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Collection;

interface UpgraderCollectionInterface
{
    /**
     * @param $element
     */
    public function add($element): void;

    /**
     * @param string $key
     * @param $element
     */
    public function set(string $key, $element): void;

    /**
     * @return bool
     */
    public function isValid(): bool;

    public function clear(): void;

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @return mixed
     */
    public function first();

    /**
     * @return mixed
     */
    public function last();

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @return bool
     */
    public function isEmpty(): bool;
}
