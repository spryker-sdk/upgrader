<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\PackagePostUpdateHandler;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Filesystem\Filesystem;
use Upgrade\Application\Dto\StepsResponseDto;

class PropelUpdateHandler implements HandlerInterface
{
    /**
     * @var string
     */
    protected const PROPEL_PACKAGE_NAME = 'propel/propel';

    /**
     * @var string
     */
    protected const PROJECT_CONSOLE_PATH = 'vendor/bin/console';

    /**
     * @var string
     */
    protected const PROPEL_SCHEMA_COPY_COMMAND = 'propel:schema:copy';

    /**
     * @var string
     */
    protected const PROPEL_MODEL_BUILD_COMMAND = 'propel:model:build';

    /**
     * @var array<string>
     */
    protected const GENERATED_MODEL_PATH_LIST = [
        'data/cache/propel',
        'src/Orm/Zed/**/Persistence/Base/',
        'src/Orm/Zed/**/Persistence/Map/',
        'src/Orm/Propel/Schema/*',
        'src/Orm/Propel/Sql/*',
        'src/Orm/Propel/generated-conf/*',
    ];

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var bool
     */
    protected bool $isReleaseGroupIntegratorEnabled;

    /**
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param bool $isReleaseGroupIntegratorEnabled
     */
    public function __construct(ProcessRunnerServiceInterface $processRunner, Filesystem $filesystem, bool $isReleaseGroupIntegratorEnabled = false)
    {
        $this->processRunner = $processRunner;
        $this->filesystem = $filesystem;
        $this->isReleaseGroupIntegratorEnabled = $isReleaseGroupIntegratorEnabled;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return bool
     */
    public function isApplicable(StepsResponseDto $stepsExecutionDto): bool
    {
        if ($this->isReleaseGroupIntegratorEnabled) {
            return false;
        }

        $composerLockDiffDto = $stepsExecutionDto->getComposerLockDiff();
        if (!$composerLockDiffDto) {
            return false;
        }

        return $this->hasPropel($composerLockDiffDto->getRequiredPackages());
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function handle(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        foreach (static::GENERATED_MODEL_PATH_LIST as $path) {
            $this->filesystem->remove((array)glob($path));
        }

        $commandList = [
            sprintf('%s %s', static::PROJECT_CONSOLE_PATH, static::PROPEL_SCHEMA_COPY_COMMAND),
            sprintf('%s %s', static::PROJECT_CONSOLE_PATH, static::PROPEL_MODEL_BUILD_COMMAND),
        ];

        foreach ($commandList as $command) {
            $response = $this->processRunner->run(explode(' ', $command), ['APPLICATION_ENV' => 'development']);
            if ($response->getExitCode()) {
                $stepsExecutionDto->addOutputMessage($response->getErrorOutput());
            }
        }

        return $stepsExecutionDto;
    }

    /**
     * @param array<\Upgrade\Domain\Entity\Package> $packages
     *
     * @return bool
     */
    protected function hasPropel(array $packages): bool
    {
        foreach ($packages as $package) {
            if ($package->getName() === static::PROPEL_PACKAGE_NAME) {
                return true;
            }
        }

        return false;
    }
}
