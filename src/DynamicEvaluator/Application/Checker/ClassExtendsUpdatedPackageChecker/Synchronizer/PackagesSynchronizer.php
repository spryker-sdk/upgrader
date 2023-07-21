<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer;

use Core\Infrastructure\Service\Filesystem;
use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PackagesSynchronizer implements PackagesSynchronizerInterface
{
    /**
     * @var string
     */
    protected const COMMAND = 'rsync -au --delete %s %s';

    /**
     * @var string
     */
    protected const GITIGNORE_FILE_NAME = '.gitignore';

    /**
     * @var \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface
     */
    protected PackagesDirProviderInterface $packagesDirProvider;

    /**
     * @var \Core\Infrastructure\Service\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunnerService;

    /**
     * @param \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface $packagesDirProvider
     * @param \Core\Infrastructure\Service\Filesystem $filesystem
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunnerService
     */
    public function __construct(
        PackagesDirProviderInterface $packagesDirProvider,
        Filesystem $filesystem,
        ProcessRunnerServiceInterface $processRunnerService
    ) {
        $this->packagesDirProvider = $packagesDirProvider;
        $this->filesystem = $filesystem;
        $this->processRunnerService = $processRunnerService;
    }

    /**
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     *
     * @return void
     */
    public function sync(): void
    {
        $fromDir = $this->packagesDirProvider->getFromDir();
        $toDir = $this->packagesDirProvider->getToDir();

        if (!$this->filesystem->exists($toDir)) {
            $this->filesystem->mkdir($toDir);
        }
        $gitignorePath = $toDir . static::GITIGNORE_FILE_NAME;
        if (!$this->filesystem->exists($gitignorePath)) {
            $this->filesystem->dumpFile($gitignorePath, '*');
        }

        try {
            foreach ($this->packagesDirProvider->getSprykerPackageDirs() as $dir) {
                $this->processRunnerService->mustRunFromCommandLine(sprintf(static::COMMAND, $fromDir . $dir, $toDir));
            }
        } catch (ProcessFailedException $e) {
            $this->clear();

            throw $e;
        }
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->filesystem->remove($this->packagesDirProvider->getToDir());
    }

    public function __destruct()
    {
        $this->clear();
    }
}
