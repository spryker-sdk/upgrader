<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Client\Composer;

use PackageManager\Domain\Client\ProcessRunnerInterface;
use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;

class ComposerCommandExecutor implements ComposerCommandExecutorInterface
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
     * @var ProcessRunnerInterface
     */
    protected ProcessRunnerInterface $processRunner;

    /**
     * @param ProcessRunnerInterface $processRunner
     */
    public function __construct(ProcessRunnerInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        $command = sprintf(
            '%s%s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
        );

        return $this->processRunner->runProcess(explode(' ', $command));
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        $command = sprintf(
            '%s%s %s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
            static::DEV_FLAG,
        );

        return $this->processRunner->runProcess(explode(' ', $command));
    }

    /**
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function update(): PackageManagerResponseDto
    {
        $command = sprintf(
            '%s %s %s',
            static::UPDATE_COMMAND_NAME,
            static::NO_SCRIPTS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
        );

        return $this->processRunner->runProcess(explode(' ', $command));
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return string
     */
    protected function getPackageString(PackageDtoCollection $packageCollection): string
    {
        $result = '';
        foreach ($packageCollection as $package) {
            $package = sprintf('%s:%s', $package->getName(), $package->getVersion());
            $result = sprintf('%s %s', $result, $package);
        }

        return $result;
    }
}
