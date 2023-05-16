<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager\CommandExecutor;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Domain\Entity\Package;

class ComposerLockComparatorCommandExecutor implements ComposerLockComparatorCommandExecutorInterface
{
    /**
     * @var string
     */
    protected const CHANGES_KEY = 'changes';

    /**
     * @var string
     */
    protected const CHANGES_DEV_KEY = 'changes-dev';

    /**
     * @var string
     */
    protected const RUNNER = '/vendor/bin/composer-lock-diff';

    /**
     * @var string
     */
    protected const JSON_OUTPUT_FLAG = '--json';

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     */
    public function __construct(ProcessRunnerServiceInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto
    {
        $command = sprintf('%s %s', APPLICATION_ROOT_DIR . static::RUNNER, static::JSON_OUTPUT_FLAG);
        $process = $this->processRunner->run(explode(' ', $command));
        $composerLockDiff = json_decode((string)$process->getOutput(), true) ?? [];

        return new ComposerLockDiffDto(
            $this->getChangesByKey($composerLockDiff, static::CHANGES_KEY),
            $this->getChangesByKey($composerLockDiff, static::CHANGES_DEV_KEY),
        );
    }

    /**
     * @param array<mixed> $composerLockDiff
     * @param string $key
     *
     * @return array<\Upgrade\Domain\Entity\Package>
     */
    protected function getChangesByKey(array $composerLockDiff, string $key): array
    {
        $packages = [];

        if (!isset($composerLockDiff[$key])) {
            return $packages;
        }

        foreach ($composerLockDiff[$key] as $packageName => $packageData) {
            $version = $packageData[1] ?? '';
            $previousVersion = $packageData[0] ?? '';
            $diffLink = $packageData[2] ?? '';

            $packages[] = new Package($packageName, $version, $previousVersion, $diffLink);
        }

        return $packages;
    }
}
