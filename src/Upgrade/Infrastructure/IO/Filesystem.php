<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\IO;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem extends SymfonyFilesystem
{
    /**
     * @var string|null
     */
    protected static ?string $lastError;

    /**
     * @param string $filename
     * @param bool $useIncludePath
     * @param mixed $context
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     *
     * @return string
     */
    public function readFile(
        string $filename,
        bool $useIncludePath = false,
        $context = null
    ): string {
        $data = $context === null
            ? static::box('file_get_contents', $filename, $useIncludePath)
            : static::box('file_get_contents', $filename, $useIncludePath, $context);

        if ($data === false) {
            throw new IOException(sprintf('Failed to read "%s" file: %s', $filename, static::$lastError));
        }

        return $data;
    }

    /**
     * @param string $directory
     * @param int $sorting_order
     * @param mixed $context
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     *
     * @return array<string>
     */
    public function scanDir(string $directory, int $sorting_order = SCANDIR_SORT_ASCENDING, $context = null): array
    {
        $data = $context === null
            ? static::box('scandir', $directory, $sorting_order)
            : static::box('scandir', $directory, $sorting_order, $context);

        if ($data === false) {
            throw new IOException(sprintf('Failed to make scan dir for "%s" directory: %s', $directory, static::$lastError));
        }

        return $data;
    }

    /**
     * @param string $func
     * @param mixed ...$args
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     *
     * @return mixed
     */
    protected static function box(string $func, ...$args)
    {
        if (!function_exists($func)) {
            throw new IOException(sprintf('Unable to perform filesystem operation because the "%s()" function has been disabled.', $func));
        }

        self::$lastError = null;

        set_error_handler([self::class, 'handleError']);
        try {
            return $func(...$args);
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @param int $type
     * @param string $msg
     *
     * @return bool
     */
    public static function handleError(int $type, string $msg): bool
    {
        self::$lastError = $msg;

        return true;
    }
}
