<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeTest\Infrastructure\PackageManager\Reader;

use Exception;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Upgrade\Infrastructure\Exception\FileNotFoundException;
use Upgrade\Infrastructure\PackageManager\Reader\ComposerLockReader;

class ComposerLockReaderTest extends TestCase
{
    /**
     * @var string
     */
    protected string $testDataDirectory;

    /**
     * @throws \Exception
     *
     * @return void
     */
    protected function setUp(): void
    {
        $testDataDirectory = realpath(dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'data');

        if ($testDataDirectory === false) {
            throw new Exception('Directory with test composer.json file doesn\'t exist');
        }

        $this->testDataDirectory = $testDataDirectory;
    }

    /**
     * @return void
     */
    public function testRead(): void
    {
        $fileStructure = [
            'composer.lock' => file_get_contents($this->testDataDirectory . DIRECTORY_SEPARATOR . 'composer.lock'),
        ];

        $fileSystem = vfsStream::setup('root', 444, $fileStructure);

        $lockReader = new ComposerLockReader();
        $lockReader->setDirectory($fileSystem->url());

        $this->assertIsArray($lockReader->read());
    }

    /**
     * @return void
     */
    public function testException(): void
    {
        $fileSystem = vfsStream::setup('root', 444, []);

        $lockReader = new ComposerLockReader();
        $lockReader->setDirectory($fileSystem->url());

        $this->expectException(FileNotFoundException::class);

        $lockReader->read();
    }
}
