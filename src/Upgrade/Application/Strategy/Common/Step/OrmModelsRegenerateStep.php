<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Filesystem\Filesystem;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Application\Strategy\StepInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class OrmModelsRegenerateStep implements StepInterface
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var string
     */
    protected const PROJECT_CONSOLE_PATH = 'vendor/bin/console';

    /**
     * @var array<string>
     */
    protected const COMMAND_LIST = [
        'transfer:generate',
        'propel:schema:copy',
        'propel:model:build',
        'transfer:entity:generate',
    ];

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
     * @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(
        ProcessRunnerServiceInterface $processRunner,
        Filesystem $filesystem,
        ConfigurationProvider $configurationProvider
    ) {
        $this->processRunner = $processRunner;
        $this->filesystem = $filesystem;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if (!$this->isApplicable($stepsExecutionDto)) {
            return $stepsExecutionDto;
        }

        return $this->handle($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return bool
     */
    protected function isApplicable(StepsResponseDto $stepsExecutionDto): bool
    {
        return (bool)$stepsExecutionDto->getComposerLockDiff();
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    protected function handle(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        foreach (static::GENERATED_MODEL_PATH_LIST as $path) {
            $this->filesystem->remove((array)glob($path));
        }

        foreach (static::COMMAND_LIST as $command) {
            $response = $this->processRunner->run([static::PROJECT_CONSOLE_PATH, $command], [
                'APPLICATION_ENV' => 'development',
                'SPRYKER_DYNAMIC_STORE_MODE' => $this->configurationProvider->isSprykerDynamicStoreModeEnabled(),
            ]);
            if ($response->getExitCode()) {
                $message = $response->getErrorOutput() ?: $response->getOutput();
                $stepsExecutionDto->addBlocker(
                    new ValidatorViolationDto(
                        'Propel model generation failed',
                        sprintf('Command: %s, response: %s', $command, $message),
                    ),
                );
                $stepsExecutionDto->addOutputMessage($message);
            }
        }

        return $stepsExecutionDto;
    }
}
