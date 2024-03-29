<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Psr\Log\LoggerInterface;
use SprykerSdk\Utils\Infrastructure\Service\Filesystem;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
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
     * @var string
     */
    protected const SPRYKER_PACKAGE_PREFIX = 'spryker';

    /**
     * @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunnerService;

    /**
     * @var \SprykerSdk\Utils\Infrastructure\Service\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManagerAdapter;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $versionControlSystem
     * @param \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface $processRunnerService
     * @param \SprykerSdk\Utils\Infrastructure\Service\Filesystem $filesystem
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManagerAdapter
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        VersionControlSystemAdapterInterface $versionControlSystem,
        ProcessRunnerServiceInterface $processRunnerService,
        Filesystem $filesystem,
        ConfigurationProvider $configurationProvider,
        PackageManagerAdapterInterface $packageManagerAdapter,
        LoggerInterface $logger
    ) {
        parent::__construct($versionControlSystem);

        $this->processRunnerService = $processRunnerService;
        $this->filesystem = $filesystem;
        $this->configurationProvider = $configurationProvider;
        $this->packageManagerAdapter = $packageManagerAdapter;
        $this->logger = $logger;
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

        $this->updateLockHash();

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

            if (!$this->isSprykerPackage($packageName) || !$this->isDigitPackageVersion($packageVersion)) {
                continue;
            }

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
     * @return void
     */
    protected function updateLockHash(): void
    {
        $response = $this->packageManagerAdapter->updateLockHash();

        if (!$response->isSuccessful()) {
            $this->logger->error(
                sprintf(
                    'Error `%s` while executing `%s`.',
                    (string)$response->getOutputMessage(),
                    implode(', ', $response->getExecutedCommands()),
                ),
            );
        }
    }

    /**
     * @return string
     */
    protected function readComposerFile(): string
    {
        return $this->filesystem->readFile($this->getComposerFilePath());
    }

    /**
     * @param string $packageName
     *
     * @return bool
     */
    protected function isSprykerPackage(string $packageName): bool
    {
        return strpos($packageName, static::SPRYKER_PACKAGE_PREFIX) === 0;
    }

    /**
     * @param string $packageVersion
     *
     * @return bool
     */
    protected function isDigitPackageVersion(string $packageVersion): bool
    {
        return (bool)preg_match('/^\d+(\.\d+)*$/', $packageVersion);
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
