<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker;

use Core\Infrastructure\Service\Filesystem;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class ProjectFileCorrupter implements EventSubscriberInterface
{
    /**
     * @var \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface
     */
    protected ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher;

    /**
     * @var \Core\Infrastructure\Service\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher
     * @param \Core\Infrastructure\Service\Filesystem $filesystem
     */
    public function __construct(ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher, Filesystem $filesystem)
    {
        $this->projectExtendedClassesFetcher = $projectExtendedClassesFetcher;
        $this->filesystem = $filesystem;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE => ['onPostRequire', 200],
        ];
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent $event
     *
     * @return void
     */
    public function onPostRequire(ReleaseGroupProcessorPostRequireEvent $event): void
    {
        $this->corrupt(5);
    }

    /**
     * @param int $filesToAffect
     *
     * @return void
     */
    public function corrupt(int $filesToAffect): void
    {
        $projectFiles = array_values($this->projectExtendedClassesFetcher->fetchExtendedClasses());
        $projectFilesCount = count($projectFiles);

        while ($filesToAffect > 0) {
            $fileName = $projectFiles[random_int(0, $projectFilesCount)];

            $fileContent = $this->filesystem->readFile($fileName);
            $fileContent = $this->replaceReturnValue($fileContent);

            $this->filesystem->dumpFile($fileName, $fileContent);

            --$filesToAffect;
        }
    }

    /**
     * @param string $fileContent
     *
     * @return string
     */
    public function replaceReturnValue(string $fileContent): string
    {
        return strtr(
            $fileContent,
            [
                ': void' => ': int',
                ': string' => ': void',
                ': int' => ': void',
                ': array' => ': void',
                ': bool' => ': void',
                ': float' => ': void',
            ],
        );
    }
}
