<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModuleComparerInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesDirsFetcherInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateFetcherInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateStorage;

class BrokenPhpFilesChecker
{
    /**
     * @var \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface
     */
    protected FileErrorsFetcherInterface $fileErrorsFetcher;

    /**
     * @var \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModuleComparerInterface
     */
    protected SprykerModuleComparerInterface $sprykerModuleComparer;

    /**
     * @var \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateStorage
     */
    protected SprykerModulesStateStorage $sprykerModulesStateStorage;

    /**
     * @var \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateFetcherInterface
     */
    protected SprykerModulesStateFetcherInterface $sprykerModulesStateFetcher;

    /**
     * @var \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesDirsFetcherInterface
     */
    protected SprykerModulesDirsFetcherInterface $sprykerModulesDirsFetcher;

    /**
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface $fileErrorsFetcher
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModuleComparerInterface $sprykerModuleComparer
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateStorage $sprykerModulesStateStorage
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesStateFetcherInterface $sprykerModulesStateFetcher
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesDirsFetcherInterface $sprykerModulesDirsFetcher
     */
    public function __construct(
        FileErrorsFetcherInterface $fileErrorsFetcher,
        SprykerModuleComparerInterface $sprykerModuleComparer,
        SprykerModulesStateStorage $sprykerModulesStateStorage,
        SprykerModulesStateFetcherInterface $sprykerModulesStateFetcher,
        SprykerModulesDirsFetcherInterface $sprykerModulesDirsFetcher
    ) {
        $this->fileErrorsFetcher = $fileErrorsFetcher;
        $this->sprykerModuleComparer = $sprykerModuleComparer;
        $this->sprykerModulesStateStorage = $sprykerModulesStateStorage;
        $this->sprykerModulesStateFetcher = $sprykerModulesStateFetcher;
        $this->sprykerModulesDirsFetcher = $sprykerModulesDirsFetcher;
    }

    /**
     * @param array<string> $composerCommands
     *
     * @return array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto>
     */
    public function checkUpdatedSprykerModules(array $composerCommands): array
    {
        $currentModulesState = $this->sprykerModulesStateFetcher->fetchCurrentSprykerModulesState();
        $previousModulesState = $this->sprykerModulesStateStorage->getModulesState();

        $updatedModules = $this->sprykerModuleComparer->compareForUpdatedModules($previousModulesState, $currentModulesState);

        if (count($updatedModules) === 0) {
            return [];
        }

        $modulesDirsToCheck = $this->sprykerModulesDirsFetcher->fetchModulesDirs($updatedModules);

        $fileErrors = $this->fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine($modulesDirsToCheck);

        if (count($fileErrors) === 0) {
            return [];
        }

        return [new ViolationDto($composerCommands, $fileErrors)];
    }

    /**
     * @return array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto>
     */
    public function checkAll(): array
    {
        $fileErrors = $this->fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        if (count($fileErrors) === 0) {
            return [];
        }

        return [new ViolationDto([], $fileErrors)];
    }

    /**
     * @return void
     */
    public function fetchAndPersistInstalledSprykerModules(): void
    {
        $modulesState = $this->sprykerModulesStateFetcher->fetchCurrentSprykerModulesState();

        $this->sprykerModulesStateStorage->setModulesState($modulesState);
    }

    /**
     * @return void
     */
    public function fetchAndPersistInitialErrors(): void
    {
        $this->fileErrorsFetcher->reset();
        $this->fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();
    }
}
