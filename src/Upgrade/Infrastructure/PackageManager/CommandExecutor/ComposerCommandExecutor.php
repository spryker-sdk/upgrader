<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager\CommandExecutor;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;

class ComposerCommandExecutor implements ComposerCommandExecutorInterface
{
    /**
     * @var array<string, int>
     */
    protected const ENV = ['COMPOSER_PROCESS_TIMEOUT' => 36000];

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
    protected const NO_PLUGINS_FLAG = '--no-plugins';

    /**
     * @var string
     */
    protected const NO_INTERACTION_FLAG = '--no-interaction';

    /**
     * @var string
     */
    protected const WITH_ALL_DEPENDENCIES_FLAG = '--with-all-dependencies';

    /**
     * @var string
     */
    protected const DEV_FLAG = '--dev';

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     */
    public function __construct(ProcessRunnerServiceInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function require(PackageCollection $packageCollection): ResponseDto
    {
        $command = sprintf(
            '%s%s %s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
        );

        $process = $this->processRunner->run(explode(' ', $command), static::ENV);

        return $this->createResponse($process);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function requireDev(PackageCollection $packageCollection): ResponseDto
    {
        $command = sprintf(
            '%s%s %s %s %s %s',
            static::REQUIRE_COMMAND_NAME,
            $this->getPackageString($packageCollection),
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
            static::WITH_ALL_DEPENDENCIES_FLAG,
            static::DEV_FLAG,
        );

        $process = $this->processRunner->run(explode(' ', $command), static::ENV);

        return $this->createResponse($process);
    }

    /**
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function update(): ResponseDto
    {
        $command = sprintf(
            '%s %s %s %s %s',
            static::UPDATE_COMMAND_NAME,
            static::WITH_ALL_DEPENDENCIES_FLAG,
            static::NO_SCRIPTS_FLAG,
            static::NO_PLUGINS_FLAG,
            static::NO_INTERACTION_FLAG,
        );

        $process = $this->processRunner->run(explode(' ', $command), static::ENV);

        return $this->createResponse($process);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return string
     */
    protected function getPackageString(PackageCollection $packageCollection): string
    {
        $result = '';
        foreach ($packageCollection->toArray() as $package) {
            $package = sprintf('%s:%s', $package->getName(), $package->getVersion());
            $result = sprintf('%s %s', $result, $package);
        }

        return $result;
    }

    /**
     * @param \Symfony\Component\Process\Process<string, string> $process
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    protected function createResponse(Process $process): ResponseDto
    {
        $command = str_replace('\'', '', $process->getCommandLine());
        $output = !$process->isSuccessful() ? $process->getErrorOutput() ?: $process->getOutput() : '';
        $outputs = array_filter([$command, $output]);

        return new ResponseDto($process->isSuccessful(), implode(PHP_EOL, $outputs));
    }
}
