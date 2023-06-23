<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager\Reader;

use Upgrade\Infrastructure\Exception\FileNotFoundException;

abstract class AbstractComposerReader implements ComposerReaderInterface
{
    /**
     * @var int
     */
    protected int $modifyTime = 0;

    /**
     * @var array<mixed>|null
     */
    protected ?array $composerData = null;

    /**
     * @var string
     */
    protected string $directory = '';

    /**
     * @return array<mixed>
     */
    abstract public function read(): array;

    /**
     * @param string $directory
     *
     * @return void
     */
    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    /**
     * @param string $path
     *
     * @throws \Upgrade\Infrastructure\Exception\FileNotFoundException
     *
     * @return array<mixed>
     */
    protected function readFromPath(string $path): array
    {
        if (!file_exists($path) || !is_file($path)) {
            throw new FileNotFoundException('File is not exist: ' . $path);
        }
        $fileTime = (int)filemtime($path);
        if (!$this->composerData || $fileTime > $this->modifyTime) {
            $this->modifyTime = $fileTime;
            $this->composerData = json_decode((string)file_get_contents($path), true);
        }

        return $this->composerData;
    }
}
