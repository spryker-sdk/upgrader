<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Composer\Fixer;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class FeaturePackageFixerStep extends AbstractFeaturePackageFixerStep
{
    /**
     * @var bool
     */
    protected bool $isReleaseGroupIntegratorEnabled;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     * @param bool $isReleaseGroupIntegratorEnabled
     */
    public function __construct(PackageManagerAdapterInterface $packageManager, bool $isReleaseGroupIntegratorEnabled = false)
    {
        $this->isReleaseGroupIntegratorEnabled = $isReleaseGroupIntegratorEnabled;

        parent::__construct($packageManager);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return bool
     */
    public function isApplicable(StepsResponseDto $stepsExecutionDto): bool
    {
        if ($this->isReleaseGroupIntegratorEnabled) {
            return false;
        }

        return parent::isApplicable($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $messages = $stepsExecutionDto->getOutputMessages();
        $foundMessages = (array)preg_grep(static::PATTERN, $messages);
        preg_match_all(static::PATTERN, (string)$stepsExecutionDto->getOutputMessage(), $matches);

        if (empty($matches[static::KEY_FEATURES]) || !is_array($matches[static::KEY_FEATURES])) {
            return $stepsExecutionDto;
        }

        $featurePackages = $this->getPackagesFromFeatures($matches[static::KEY_FEATURES]);

        $packageCollection = new PackageCollection($featurePackages);

        $responseDto = $this->packageManager->require($packageCollection);

        $stepsExecutionDto->setIsSuccessful($responseDto->isSuccessful());
        if (!$responseDto->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage($responseDto->getOutputMessage());

            return $stepsExecutionDto;
        }

        $responseDto = $this->packageManager->remove(new PackageCollection(array_map(
            fn (string $featurePackage): Package => new Package($featurePackage),
            $matches[static::KEY_FEATURES],
        )));
        $stepsExecutionDto->setIsSuccessful($responseDto->isSuccessful());

        if (!$responseDto->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage($responseDto->getOutputMessage());

            return $stepsExecutionDto;
        }

        foreach ($foundMessages as $key => $foundMessage) {
            unset($messages[$key]);
        }
        $stepsExecutionDto->setOutputMessages($messages);
        $stepsExecutionDto->addOutputMessage(sprintf('Splitted %s feature package(s)', count($matches[static::KEY_FEATURES])));

        return $stepsExecutionDto;
    }

    /**
     * @param array<string> $featurePackages
     *
     * @return array<\Upgrade\Domain\Entity\Package>
     */
    protected function getPackagesFromFeatures(array $featurePackages): array
    {
        $composerLockFile = $this->packageManager->getComposerLockFile();
        $packages = [];
        if (!isset($composerLockFile['packages'])) {
            return [];
        }

        foreach ($composerLockFile['packages'] as $lockPackage) {
            foreach ($featurePackages as $featurePackage) {
                if ($lockPackage['name'] !== $featurePackage) {
                    continue;
                }

                unset($lockPackage['require']['php']);
                $packages[] = $lockPackage['require'];
            }
        }
        $packages = array_merge(...$packages);

        return array_map(
            fn (string $featurePackage, string $version): Package => new Package($featurePackage, $version),
            array_keys($packages),
            array_values($packages),
        );
    }
}
