<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Enum;

use ReflectionClass;
use Throwable;
use Upgrader\Business\Exception\UpgraderException;

class UpgraderEnum
{
    /**
     * @var array
     */
    protected static $cache = [];

    /**
     * @var array
     */
    protected static $objectCache = [];

    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     */
    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new UpgraderException("Value '$value' is not part of the enum " . static::class);
        }

        $class = static::class;
        static::$objectCache[$class][$value] = $this;

        $this->value = $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function isValid($value): bool
    {
        try {
            $value = (string)$value;
        } catch (Throwable $exception) {
            return false;
        }

        return in_array($value, self::toArray(), true);
    }

    /**
     * @return array
     */
    public static function toArray(): array
    {
        $class = static::class;
        if (!array_key_exists($class, static::$cache)) {
            try {
                $reflection = new ReflectionClass($class);
                static::$cache[$class] = $reflection->getConstants();
            } catch (Throwable $e) {
                static::$cache[$class] = [];
            }
        }

        return static::$cache[$class];
    }
}
