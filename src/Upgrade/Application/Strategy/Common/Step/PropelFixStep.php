<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\StepInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class PropelFixStep implements StepInterface
{
    /**
     * @var string
     */
    public const PACKAGE_NAME = 'propel/propel';

    /**
     * @var string
     */
    public const LOCK_PACKAGE_VERSION = '2.0.0-beta2';

    protected PackageManagerAdapterInterface $packageManager;

    protected bool $isReleaseGroupIntegratorEnabled;

    public function __construct(PackageManagerAdapterInterface $packageManager, bool $isReleaseGroupIntegratorEnabled = false)
    {
        $this->packageManager = $packageManager;
        $this->isReleaseGroupIntegratorEnabled = $isReleaseGroupIntegratorEnabled;
    }

    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if ($this->isReleaseGroupIntegratorEnabled) {
            return $stepsExecutionDto;
        }

        $packageVersion = $this->packageManager->getPackageVersion(static::PACKAGE_NAME);

        if ($packageVersion !== static::LOCK_PACKAGE_VERSION) {
            return $stepsExecutionDto;
        }

        if ($this->alreadyHasRequiredPropelPackage()) {
            return $stepsExecutionDto;
        }

        $response = $this->packageManager->require(
            new PackageCollection([
                new Package(static::PACKAGE_NAME, static::LOCK_PACKAGE_VERSION),
            ]),
        );

        if (!$response->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage('Could not require propel package');
        }

        return $stepsExecutionDto;
    }

    protected function alreadyHasRequiredPropelPackage(): bool
    {
        $composerJson = $this->packageManager->getComposerJsonFile();

        $packages = array_merge($composerJson['require'], $composerJson['require-dev'] ?? []);

        return array_key_exists(static::PACKAGE_NAME, $packages);
    }
}
