<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule;

use Core\Infrastructure\Service\FinderFactory;
use DynamicEvaluator\Application\ProjectConfigReader\ProjectConfigReaderInterface;
use SprykerSdk\Utils\Infrastructure\Helper\StrHelper;
use Upgrader\Configuration\ConfigurationProvider;

class SprykerModulesDirsFetcher implements SprykerModulesDirsFetcherInterface
{
    /**
     * @var \DynamicEvaluator\Application\ProjectConfigReader\ProjectConfigReaderInterface
     */
    protected ProjectConfigReaderInterface $projectConfigReader;

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Core\Infrastructure\Service\FinderFactory
     */
    protected FinderFactory $finderFactory;

    /**
     * @param \DynamicEvaluator\Application\ProjectConfigReader\ProjectConfigReaderInterface $projectConfigReader
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \Core\Infrastructure\Service\FinderFactory $finderFactory
     */
    public function __construct(
        ProjectConfigReaderInterface $projectConfigReader,
        ConfigurationProvider $configurationProvider,
        FinderFactory $finderFactory
    ) {
        $this->projectConfigReader = $projectConfigReader;
        $this->configurationProvider = $configurationProvider;
        $this->finderFactory = $finderFactory;
    }

    /**
     * @param array<string> $sprykerPackages List of modules
     *
     * @return array<string> List of local modules dirs
     */
    public function fetchModulesDirs(array $sprykerPackages): array
    {
        $modulesDirs = [];

        foreach ($this->projectConfigReader->getProjectNamespaces() as $projectNamespace) {
            $modulesDirs[] = $this->getNamespaceModuleDirs($sprykerPackages, $projectNamespace);
        }

        return array_unique(array_merge(...$modulesDirs));
    }

    /**
     * @param array<string> $sprykerPackages
     * @param string $projectNamespace
     *
     * @return array<string>
     */
    protected function getNamespaceModuleDirs(array $sprykerPackages, string $projectNamespace): array
    {
        $modulesDirs = [];

        foreach ($sprykerPackages as $sprykerPackage) {
            $modulesDirs[] = $this->findAllPackageDirs($sprykerPackage, $projectNamespace);
        }

        return array_merge(...$modulesDirs);
    }

    /**
     * @param string $sprykerPackage
     * @param string $projectNamespace
     *
     * @return array<string>
     */
    protected function findAllPackageDirs(string $sprykerPackage, string $projectNamespace): array
    {
        $modulesDirs = [];

        $globPattern = $this->configurationProvider->getRootPath()
            . 'src' . DIRECTORY_SEPARATOR
            . $projectNamespace . DIRECTORY_SEPARATOR
            . '*' . DIRECTORY_SEPARATOR;

        $finder = $this->finderFactory->createFinder();

        $finder->depth(0)
            ->path(sprintf('/^%s$/', preg_quote($this->sprykerPackageToModuleDir($sprykerPackage), '/')))
            ->directories();

        foreach ($finder->in($globPattern) as $dir) {
            $modulesDirs[] = $dir->getPathname();
        }

        return $modulesDirs;
    }

    /**
     * @param string $package
     *
     * @return string
     */
    protected function sprykerPackageToModuleDir(string $package): string
    {
        [, $packageName] = explode('/', $package);

        return StrHelper::dashToCamelCase($packageName);
    }
}
