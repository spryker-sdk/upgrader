<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer;

use Upgrader\Business\Composer\CommandExecutor\UpdateCommand;
use Upgrader\Business\Composer\ComposerJson\ComposerJsonReader;
use Upgrader\Business\Composer\ComposerJson\ComposerJsonWriter;
use Upgrader\Business\Composer\ComposerLock\ComposerLockReader;
use Upgrader\Business\Composer\Helper\JsonFile\JsonFileHelperFactory;

class ComposerClientFactory
{
    /**
     * @var \Upgrader\Business\Git\CommandResolver\UpdateIndexCommand
     */
    private $updateCommand;

    /**
     * @var \Upgrader\Business\Composer\ComposerJson\ComposerJsonReader
     */
    private $composerJsonReader;

    /**
     * @var \Upgrader\Business\Composer\ComposerJson\ComposerJsonWriter
     */
    private $composerJsonWriter;

    /**
     * @var \Upgrader\Business\Composer\ComposerLock\ComposerLockReader
     */
    private $composerLockReader;

    /**
     * @var \Upgrader\Business\Composer\Helper\JsonFile\JsonFileHelperFactory
     */
    private $jsonFileHelperFactory;

    /**
     * @return \Upgrader\Business\Composer\CommandExecutor\UpdateCommand
     */
    public function createUpdateCommand(): UpdateCommand
    {
        if ($this->updateCommand === null) {
            $this->updateCommand = new UpdateCommand();
        }

        return $this->updateCommand;
    }

    /**
     * @return \Upgrader\Business\Composer\ComposerJson\ComposerJsonReader
     */
    public function createComposerJsonReader(): ComposerJsonReader
    {
        if ($this->composerJsonReader === null) {
            $jsonFileReadHelper = $this->createJsonFileHelperFactory()->createJsonFileReader();
            $this->composerJsonReader = new ComposerJsonReader($jsonFileReadHelper);
        }

        return $this->composerJsonReader;
    }

    /**
     * @return \Upgrader\Business\Composer\ComposerJson\ComposerJsonWriter
     */
    public function createComposerJsonWriter(): ComposerJsonWriter
    {
        if ($this->composerJsonWriter === null) {
            $jsonFileWriteHelper = $this->createJsonFileHelperFactory()->createJsonFileWriter();
            $this->composerJsonWriter = new ComposerJsonWriter($jsonFileWriteHelper);
        }

        return $this->composerJsonWriter;
    }

    /**
     * @return \Upgrader\Business\Composer\ComposerLock\ComposerLockReader
     */
    public function createComposerLockReader(): ComposerLockReader
    {
        if ($this->composerLockReader === null) {
            $jsonFileReadHelper = $this->createJsonFileHelperFactory()->createJsonFileReader();
            $this->composerLockReader = new ComposerLockReader($jsonFileReadHelper);
        }

        return $this->composerLockReader;
    }

    /**
     * @return \Upgrader\Business\Composer\Helper\JsonFile\JsonFileHelperFactory
     */
    public function createJsonFileHelperFactory(): JsonFileHelperFactory
    {
        if ($this->jsonFileHelperFactory === null) {
            $this->jsonFileHelperFactory = new JsonFileHelperFactory();
        }

        return $this->jsonFileHelperFactory;
    }
}
