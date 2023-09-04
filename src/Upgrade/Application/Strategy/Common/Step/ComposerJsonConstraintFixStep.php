<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Core\Infrastructure\Service\Filesystem;
use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\StepInterface;
use Upgrader\Configuration\ConfigurationProvider;

class ComposerJsonConstraintFixStep extends AbstractStep implements StepInterface
{
    /**
     * @var int
     */
    protected const GREP_FOUND_PACKAGES_EXIT_CODE = 0;

    /**
     * @var string
     */
    protected const COMPOSER_JSON_FILE = 'composer.json';

    /**
     * @var string
     */
    protected const COMPOSER_PACKAGE_REGEXP = '[a-z0-9]([_.-]?[a-z0-9]+)*/[a-z0-9]([_.-]?[a-z0-9]+)*';

    /**
     * @var string
     */
    protected const VERSION_CONSTRAINT_PREFIX = '^';

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunnerService;

    /**
     * @var \Core\Infrastructure\Service\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $versionControlSystem
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunnerService
     * @param \Core\Infrastructure\Service\Filesystem $filesystem
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(
        VersionControlSystemAdapterInterface $versionControlSystem,
        ProcessRunnerServiceInterface $processRunnerService,
        Filesystem $filesystem,
        ConfigurationProvider $configurationProvider
    ) {
        parent::__construct($versionControlSystem);

        $this->processRunnerService = $processRunnerService;
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
        $packages = $this->getPackagesNamesFromDiff();

        if (count($packages) === 0) {
            return $stepsExecutionDto;
        }

        $composerJsonContent = $this->readComposerFile();
        $composerJsonContent = $this->addCaretToVersionConstraints($composerJsonContent, $packages);

        $this->writeComposerFile($composerJsonContent);

        return $stepsExecutionDto;
    }

    /**
     * @return array<string>
     */
    protected function getPackagesNamesFromDiff(): array
    {
        $packageRegexp = static::COMPOSER_PACKAGE_REGEXP;

        $command = sprintf(
            <<<CMD
            git diff %s | grep -E '\+\s+"%s" *: *"\d.*"'
            CMD,
            static::COMPOSER_JSON_FILE,
            $packageRegexp,
        );

        $process = $this->processRunnerService->runFromCommandLine($command, $this->configurationProvider->getRootPath());

        if ($process->getExitCode() !== static::GREP_FOUND_PACKAGES_EXIT_CODE) {
            return [];
        }

        preg_match_all(
            sprintf('#(?<packages>"%s" *: *".+")#', static::COMPOSER_PACKAGE_REGEXP),
            $process->getOutput(),
            $matches,
        );

        if (!isset($matches['packages']) || count($matches['packages']) === 0) {
            return [];
        }

        return $matches['packages'];
    }

    /**
     * @param string $composerJsonContent
     * @param array<string> $packages
     *
     * @return string
     */
    protected function addCaretToVersionConstraints(string $composerJsonContent, array $packages): string
    {
        $updatedComposerJson = $composerJsonContent;

        foreach ($packages as $package) {
            [$packageName, $packageVersion] = array_map(static fn (string $part) => trim($part, ' "'), explode(':', $package));

            $updatedComposerJson = preg_replace(
                sprintf('#"(%s)"(\s*):(\s*)"(%s)"#', preg_quote($packageName, '#'), preg_quote($packageVersion, '#')),
                sprintf('"$1"$2:$3"%s$4"', static::VERSION_CONSTRAINT_PREFIX),
                $updatedComposerJson,
                1,
            );

            // @codeCoverageIgnoreStart
            if ($updatedComposerJson === null) {
                return $composerJsonContent;
            }
            // @codeCoverageIgnoreEnd
        }

        return $updatedComposerJson;
    }

    /**
     * @return string
     */
    protected function readComposerFile(): string
    {
        return $this->filesystem->readFile($this->getComposerFilePath());
    }

    /**
     * @param string $content
     *
     * @return void
     */
    protected function writeComposerFile(string $content): void
    {
        $this->filesystem->dumpFile($this->getComposerFilePath(), $content);
    }

    /**
     * @return string
     */
    protected function getComposerFilePath(): string
    {
        return $this->configurationProvider->getRootPath() . static::COMPOSER_JSON_FILE;
    }
}
