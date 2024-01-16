<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use Psr\Log\LoggerInterface;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManagerResponseDto;

class ReleaseGroupUpgrader
{
    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher
     */
    protected ModuleFetcher $moduleFetcher;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var array<\Upgrade\Application\Strategy\UpgradeFixerInterface>
     */
    protected array $fixers = [];

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher $moduleFetcher
     * @param \Psr\Log\LoggerInterface $logger
     * @param array<\Upgrade\Application\Strategy\UpgradeFixerInterface> $fixers
     */
    public function __construct(ModuleFetcher $moduleFetcher, LoggerInterface $logger, array $fixers = [])
    {
        $this->moduleFetcher = $moduleFetcher;
        $this->logger = $logger;
        $this->fixers = $fixers;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function upgrade(ReleaseGroupDto $releaseGroup): PackageManagerResponseDto
    {
        $alternativeRequireModuleCollections = $this->getAlternativeRequireModuleCollections($releaseGroup);
        $alternativeRequireModuleCollections[] = $releaseGroup->getModuleCollection();

        foreach ($alternativeRequireModuleCollections as $alternativeRequireModuleCollection) {
            $response = $this->moduleFetcher->require($alternativeRequireModuleCollection);
            if ($response->isSuccessful()) {
                break;
            }
        }

        if (!$response->isSuccessful()) {
            $response = $this->runWithFixer($releaseGroup, $response);
        }

        return $response;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     *
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection>
     */
    protected function getAlternativeRequireModuleCollections(ReleaseGroupDto $releaseGroup): array
    {
        $moduleAlternativeCollection = [];
        if ($releaseGroup->getFeaturePackages()->count()) {
            $packageCollection = clone $releaseGroup->getModuleCollection();
            foreach ($releaseGroup->getFeaturePackages()->toArray() as $package) {
                $packageCollection->add($package);
            }
            $moduleAlternativeCollection[] = $packageCollection;
        }

        return $moduleAlternativeCollection;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     * @param \Upgrade\Application\Dto\PackageManagerResponseDto $response
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    protected function runWithFixer(ReleaseGroupDto $releaseGroup, PackageManagerResponseDto $response): PackageManagerResponseDto
    {
        $this->logger->info(
            sprintf('Release Group `%s` is failed. Trying to fix it', $releaseGroup->getId()),
            [$response->getOutputMessage()],
        );

        foreach ($this->fixers as $fixer) {
            $fixerNamespacePaths = explode('\\', get_class($fixer));
            $fixerName = end($fixerNamespacePaths);
            if (!$fixer->isApplicable($releaseGroup, $response)) {
                $this->logger->info(sprintf('Fixer `%s` is not applicable', $fixerName));

                continue;
            }

            $this->logger->info(sprintf('`%s` fixer is applying', $fixerName));

            $fixerResponse = $fixer->run($releaseGroup, $response);

            if ($fixerResponse !== null && !$fixerResponse->isSuccessful()) {
                $this->logger->warning(
                    sprintf('Fixer `%s` is failed', $fixerName),
                    [$response->getOutputMessage()],
                );

                continue;
            }
            if ($fixerResponse !== null) {
                $response = $fixerResponse;
            }

            if ($fixer->isReRunStep()) {
                $this->logger->info('Run release group upgrade after fixer.');

                $response = $this->moduleFetcher->require($releaseGroup->getModuleCollection());
            }

            if ($response->isSuccessful()) {
                break;
            }
        }

        return $response;
    }
}
