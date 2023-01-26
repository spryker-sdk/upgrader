<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Composer\Fixer;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\FixerStepInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class FeaturePackageFixerStep implements FixerStepInterface
{
    /**
     * @var string
     */
    protected const PATTERN = '/(?<features>[-\w]+-feature\/[-\w]+).+conflicts.+/';

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(
        PackageManagerAdapterInterface $packageManager
    ) {
        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return bool
     */
    public function isApplicable(StepsResponseDto $stepsExecutionDto): bool
    {
        return !$stepsExecutionDto->getIsSuccessful() &&
            $stepsExecutionDto->getOutputMessage() !== null &&
            preg_match(static::PATTERN, $stepsExecutionDto->getOutputMessage());
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

        if (empty($matches['features']) || !is_array($matches['features'])) {
            return $stepsExecutionDto;
        }

        $featurePackages = $this->getFeaturePackages($matches['features']);
        $responseDto = $this->packageManager->require(new PackageCollection($featurePackages));

        $stepsExecutionDto->setIsSuccessful($responseDto->isSuccessful());
        if (!$responseDto->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage($responseDto->getOutputMessage());

            return $stepsExecutionDto;
        }

        $responseDto = $this->packageManager->remove(new PackageCollection(array_map(
            fn (string $featurePackage): Package => new Package($featurePackage),
            $matches['features'],
        )));
        $stepsExecutionDto->setIsSuccessful($responseDto->isSuccessful());
        if (!$responseDto->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage($responseDto->getOutputMessage());
        }

        if ($responseDto->isSuccessful()) {
            foreach ($foundMessages as $key => $foundMessage) {
                unset($messages[$key]);
            }
            $stepsExecutionDto->setOutputMessages($messages);
            $stepsExecutionDto->addOutputMessage(sprintf('Splitted %s feature packages', count($matches['features'])));
        }

        return $stepsExecutionDto;
    }

    /**
     * @param array<string> $featurePackages
     *
     * @return array<\Upgrade\Domain\Entity\Package>
     */
    protected function getFeaturePackages(array $featurePackages): array
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
