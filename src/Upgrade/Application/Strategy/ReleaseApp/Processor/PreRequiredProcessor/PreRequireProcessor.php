<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;

class PreRequireProcessor implements PreRequireProcessorInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor\PreRequireProcessorStrategyInterface> $processorStrategies
     */
    protected array $processorStrategies;

    /**
     * @param array<\Upgrade\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor\PreRequireProcessorStrategyInterface> $processorStrategies
     */
    public function __construct(array $processorStrategies)
    {
        $this->processorStrategies = $processorStrategies;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $requireCollection
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection
     */
    public function process(ReleaseGroupDtoCollection $requireCollection): ReleaseGroupDtoCollection
    {
        foreach ($this->processorStrategies as $processorStrategy) {
            $requireCollection = $processorStrategy->process($requireCollection);
        }

        return $requireCollection;
    }
}
