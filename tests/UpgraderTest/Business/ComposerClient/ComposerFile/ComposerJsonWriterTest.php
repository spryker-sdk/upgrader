<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgraderTest\Business\ComposerClient\ComposerFile;

use Codeception\Test\Unit;
use Ergebnis\Json\Printer\Printer;
use Upgrader\Business\PackageManager\Client\Composer\Json\Writer\ComposerJsonWriter;

class ComposerJsonWriterTest extends Unit
{
    /**
     * @return void
     */
    public function testWriteDefaultIndentation(): void
    {
        // Arrange
        $path = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $generatedPath = $path . '_generated' . DIRECTORY_SEPARATOR;
        $checkFile = $path . 'composer4space.json';
        $writeFile = $generatedPath . 'composer4space.json';
        if (!file_exists($generatedPath)) {
            mkdir($generatedPath, 0777, true);
        }
        file_put_contents($writeFile, ' ');

        $printer = new Printer();
        $composerJsonWriter = $this->construct(
            ComposerJsonWriter::class,
            ['printer' => $printer],
            ['getFileName' => $writeFile]
        );

        $array = [
            'name' => 'foo/bar',
            'require' => [
                'php' => '^7.4',
            ],
        ];

        // Act
        $result = $composerJsonWriter->write($array);

        // Assert
        $this->assertTrue($result);

        $content = file_get_contents($writeFile);

        $this->assertStringEqualsFile($checkFile, $content);
    }

    /**
     * @return void
     */
    public function testWriteTwoSpaceIndentation(): void
    {
        // Arrange
        $path = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $generatedPath = $path . '_generated' . DIRECTORY_SEPARATOR;
        $checkFile = $path . 'composer2space.json';
        $writeFile = $generatedPath . 'composer2space.json';
        if (!file_exists($generatedPath)) {
            mkdir($generatedPath, 0777, true);
        }
        copy($checkFile, $writeFile);

        $printer = new Printer();
        $composerJsonWriter = $this->construct(
            ComposerJsonWriter::class,
            ['printer' => $printer],
            ['getFileName' => $writeFile]
        );

        $array = [
            'name' => 'foo/bar',
            'require' => [
                'php' => '^7.4',
            ],
        ];

        // Act
        $result = $composerJsonWriter->write($array);

        // Assert
        $this->assertTrue($result);

        $content = file_get_contents($writeFile);

        $this->assertStringEqualsFile($checkFile, $content);
    }
}
