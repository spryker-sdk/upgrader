<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher;

use Core\Infrastructure\Service\FinderFactory;
use DynamicEvaluator\Application\ProjectConfigReader\ProjectConfigReaderInterface;
use Symfony\Component\Finder\Finder;
use Upgrader\Configuration\ConfigurationProvider;

class ProjectModulesNamesFetcher implements ProjectModulesNamesFetcherInterface
{
    /**
     * @var array<string>
     */
    public const MODULE_LAYERS = ['Client', 'Glue', 'Service', 'Shared', 'Yves', 'Zed'];

    protected ProjectConfigReaderInterface $projectConfigReader;

    protected FinderFactory $finderFactory;

    protected ConfigurationProvider $configurationProvider;

    public function __construct(
        ProjectConfigReaderInterface $projectConfigReader,
        FinderFactory $finderFactory,
        ConfigurationProvider $configurationProvider
    ) {
        $this->projectConfigReader = $projectConfigReader;
        $this->finderFactory = $finderFactory;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return array<string>
     */
    public function fetchProjectModules(): array
    {
        $finder = $this->finderFactory->createFinder();
        $finder
            ->directories()
            ->depth('== 0');

        $srcPath = rtrim($this->configurationProvider->getRootPath(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

        $projectModules = [];
        foreach ($this->projectConfigReader->getProjectNamespaces() as $projectNamespace) {
            $projectModules[] = $this->getModulesFromProjectNamespace($projectNamespace, $finder, $srcPath);
        }

        return array_unique(array_merge(...$projectModules));
    }

    /**
     * @return array<string>
     */
    protected function getModulesFromProjectNamespace(string $projectNamespace, Finder $finder, string $srcPath): array
    {
        $lookUpPaths = array_map(static fn (string $dir): string => $srcPath . $projectNamespace . DIRECTORY_SEPARATOR . $dir, static::MODULE_LAYERS);
        $modulesDirs = [];

        foreach ($finder->in($lookUpPaths) as $dir) {
            $modulesDirs[] = $dir->getFilename();
        }

        return $modulesDirs;
    }
}
