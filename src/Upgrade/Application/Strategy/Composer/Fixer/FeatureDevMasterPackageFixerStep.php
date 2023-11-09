<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Composer\Fixer;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Factory\ComposerViolationDtoFactory;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class FeatureDevMasterPackageFixerStep extends AbstractFeaturePackageFixerStep
{
    /**
     * @var string
     */
    public const MASK_ALIAS_DEV_MASTER = 'dev-master as ^%s';

    /**
     * @var bool
     */
    protected bool $isFeatureToDevMasterEnabled;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     * @param bool $isFeatureToDevMasterEnabled
     */
    public function __construct(PackageManagerAdapterInterface $packageManager, bool $isFeatureToDevMasterEnabled = false)
    {
        $this->isFeatureToDevMasterEnabled = $isFeatureToDevMasterEnabled;

        parent::__construct($packageManager);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return bool
     */
    public function isApplicable(StepsResponseDto $stepsExecutionDto): bool
    {
        return $this->isFeatureToDevMasterEnabled && parent::isApplicable($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        preg_match_all(static::FEATURE_PACKAGE_PATTERN, (string)$stepsExecutionDto->getOutputMessage(), $matches);

        if (!isset($matches[static::KEY_FEATURES]) || !$matches[static::KEY_FEATURES] || !is_array($matches[static::KEY_FEATURES])) {
            return $stepsExecutionDto;
        }

        $version = sprintf(
            static::MASK_ALIAS_DEV_MASTER,
            date('Y', strtotime(date('m') <= 11 ? 'now' : '+1 year')) . '00.0',
        );

        $packageCollection = new PackageCollection(array_map(
            fn (string $featurePackage): Package => new Package($featurePackage, $version),
            $matches[static::KEY_FEATURES],
        ));

        $responseDto = $this->packageManager->require($packageCollection);

        $stepsExecutionDto->setIsSuccessful($responseDto->isSuccessful());

        if (!$responseDto->isSuccessful()) {
            $stepsExecutionDto->addOutputMessage($responseDto->getOutputMessage());

            return $stepsExecutionDto;
        }
        $messages = $stepsExecutionDto->getOutputMessages();
        $foundMessages = (array)preg_grep(static::FEATURE_PACKAGE_PATTERN, $messages);
        foreach ($foundMessages as $key => $foundMessage) {
            unset($messages[$key]);
        }
        $stepsExecutionDto->setOutputMessages($messages);
        $stepsExecutionDto->addOutputMessage(sprintf('Versions were changed to %s for %s feature package(s)', static::MASK_ALIAS_DEV_MASTER, count($matches[static::KEY_FEATURES])));

        if ($stepsExecutionDto->getIsSuccessful()) {
            $stepsExecutionDto->removeBlockersByTitle(ComposerViolationDtoFactory::VIOLATION_TITLE);
        }

        return $stepsExecutionDto;
    }
}
