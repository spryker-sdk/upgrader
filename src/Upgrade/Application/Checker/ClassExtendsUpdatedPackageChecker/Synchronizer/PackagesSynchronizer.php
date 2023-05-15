<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PackagesSynchronizer implements PackagesSynchronizerInterface
{
    /**
     * @var string
     */
    protected const COMMAND = 'rsync -au --delete %s %s';

    /**
     * @var \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface
     */
    protected PackagesDirProviderInterface $packagesDirProvider;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface $packagesDirProvider
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(PackagesDirProviderInterface $packagesDirProvider, Filesystem $filesystem)
    {
        $this->packagesDirProvider = $packagesDirProvider;
        $this->filesystem = $filesystem;
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

        try {
            foreach ($this->packagesDirProvider->getSprykerPackageDirs() as $dir) {
                $process = Process::fromShellCommandline(
                    sprintf(static::COMMAND, $fromDir . $dir, $toDir),
                );
                $process->mustRun();
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
}
