<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\PackagePostUpdateHandler;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
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
    protected const TRANSFER_GENERATE_COMMAND = 'transfer:generate';

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
        'src/Generated/',
        'src/Orm/Propel/*',
    ];

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
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return bool
     */
    public function isApplicable(StepsResponseDto $stepsExecutionDto): bool
    {
        $composerLockDiffDto = $stepsExecutionDto->getComposerLockDiff();
        if (!$composerLockDiffDto) {
            return false;
        }

        return $this->hasPropel($composerLockDiffDto->getRequireChanges());
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function handle(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $commandList = [
            sprintf('rm -rf %s', implode(' ', self::GENERATED_MODEL_PATH_LIST)),
            sprintf('%s %s', static::PROJECT_CONSOLE_PATH, static::TRANSFER_GENERATE_COMMAND),
            sprintf('%s %s', static::PROJECT_CONSOLE_PATH, static::PROPEL_SCHEMA_COPY_COMMAND),
            sprintf('%s %s', static::PROJECT_CONSOLE_PATH, static::PROPEL_MODEL_BUILD_COMMAND),
        ];

        foreach ($commandList as $command) {
            $response = $this->processRunner->run(explode(' ', $command));
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
