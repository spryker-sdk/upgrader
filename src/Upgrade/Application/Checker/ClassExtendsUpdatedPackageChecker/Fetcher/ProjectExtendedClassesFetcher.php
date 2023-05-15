<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

use Symfony\Component\Finder\Finder;
use Upgrade\Infrastructure\IO\FinderFactory;
use Upgrader\Configuration\ConfigurationProvider;

class ProjectExtendedClassesFetcher implements ProjectExtendedClassesFetcherInterface
{
    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var array<string, string>|null
     */
    protected ?array $foundClasses = null;

    /**
     * @var \Upgrade\Infrastructure\IO\FinderFactory
     */
    protected FinderFactory $finderFactory;

    /**
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \Upgrade\Infrastructure\IO\FinderFactory $finderFactory
     */
    public function __construct(ConfigurationProvider $configurationProvider, FinderFactory $finderFactory)
    {
        $this->configurationProvider = $configurationProvider;
        $this->finderFactory = $finderFactory;
    }

    /**
     * @return array<string, string>
     */
    public function fetchExtendedClasses(): array
    {
        if ($this->foundClasses === null) {
            $this->foundClasses = $this->findExtendedClasses();
        }

        return $this->foundClasses;
    }

    /**
     * @return array<string, string>
     */
    protected function findExtendedClasses(): array
    {
        $classNames = [];

        foreach ($this->getFinderIterator() as $file) {
            $fileContent = $file->getContents();

            preg_match('/ extends (?<extendedClass>\S+)/', $fileContent, $matches);

            if (!isset($matches['extendedClass'])) {
                continue;
            }

            preg_match(sprintf('/use (?<useClass>(\S*)(%s| as %s))/', $matches['extendedClass'], $matches['extendedClass']), $fileContent, $matches);

            $className = explode(' as ', $matches['useClass'])[0];

            $classNames[$className] = $file->getRealPath();
        }

        return $classNames;
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     */
    protected function getFinderIterator(): Finder
    {
        $finder = $this->finderFactory->createFinder();
        $finder->files()
            ->name('*.php')
            ->notName('*Trait.php')
            ->notName('*Interface.php');

        return $finder->in($this->configurationProvider->getRootPath() . 'src')
            ->exclude('Generated')
            ->exclude('Orm');
    }
}
