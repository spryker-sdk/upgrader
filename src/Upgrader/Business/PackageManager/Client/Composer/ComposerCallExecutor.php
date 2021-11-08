<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer;

use Upgrader\Business\PackageManager\CallExecutor\CallExecutor;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;

class ComposerCallExecutor implements ComposerCallExecutorInterface
{
    /**
     * @var string
     */
    protected const REQUIRE_COMMAND_NAME = 'composer require';

    /**
     * @var string
     */
    protected const UPDATE_COMMAND_NAME = 'composer update';

    /**
     * @var string
     */
    protected const NO_SCRIPTS_FLAG = '--no-scripts';

    /**
     * @var string
     */
    protected const WITH_ALL_DEPENDENCIES_FLAG = '--with-all-dependencies';

    /**
     * @var string
     */
    protected const DEV_FLAG = '--dev';

    /**
     * @var \Upgrader\Business\PackageManager\CallExecutor\CallExecutor
     */
    protected $callExecutor;

    /**
     * @param \Upgrader\Business\PackageManager\CallExecutor\CallExecutor $callExecutor
     */
    public function __construct(CallExecutor $callExecutor)
    {
        $this->callExecutor = $callExecutor;
    }

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function require(PackageTransferCollection $packageCollection): PackageManagerResponse
    {
        $command = sprintf(
            '%s%s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
        );
        $process = $this->callExecutor->runProcess($command);

        return $this->callExecutor->createResponse($process);
    }

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function requireDev(PackageTransferCollection $packageCollection): PackageManagerResponse
    {
        $command = sprintf(
            '%s%s %s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
            static::DEV_FLAG,
        );
        $process = $this->callExecutor->runProcess($command);

        return $this->callExecutor->createResponse($process);
    }

    /**
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function update(): PackageManagerResponse
    {
        $command = sprintf(
            '%s %s %s',
            static::UPDATE_COMMAND_NAME,
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
        );
        $process = $this->callExecutor->runProcess($command);

        return $this->callExecutor->createResponse($process);
    }

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return string
     */
    protected function getPackageString(PackageTransferCollection $packageCollection): string
    {
        $result = '';
        foreach ($packageCollection as $package) {
            $package = sprintf('%s:%s', $package->getName(), $package->getVersion());
            $result = sprintf('%s %s', $result, $package);
        }

        return $result;
    }
}
