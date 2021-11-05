<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Strategy;

use Upgrader\Business\Upgrader\Request\UpgraderRequest;

class UpdateStrategyGenerator implements UpdateStrategyGeneratorInterface
{
    /**
     * @var string
     */
    public const COMPOSER_UPDATE = 'composer-update';

    /**
     * @var string
     */
    public const RELEASE_GROUP = 'release-group';

    /**
     * @var \Upgrader\Business\Upgrader\Strategy\UpgradeStrategyInterface
     */
    protected $composerUpdateStrategy;

    /**
     * @var \Upgrader\Business\Upgrader\Strategy\UpgradeStrategyInterface
     */
    protected $releaseGroupStrategy;

    /**
     * @param \Upgrader\Business\Upgrader\Strategy\UpgradeStrategyInterface $composerUpdateStrategy
     * @param \Upgrader\Business\Upgrader\Strategy\UpgradeStrategyInterface $releaseGroupStrategy
     */
    public function __construct(
        UpgradeStrategyInterface $composerUpdateStrategy,
        UpgradeStrategyInterface $releaseGroupStrategy
    ) {
        $this->composerUpdateStrategy = $composerUpdateStrategy;
        $this->releaseGroupStrategy = $releaseGroupStrategy;
    }

    /**
     * @param \Upgrader\Business\Upgrader\Request\UpgraderRequest $request
     *
     * @return \Upgrader\Business\Upgrader\Strategy\UpgradeStrategyInterface
     */
    public function getStrategy(UpgraderRequest $request): UpgradeStrategyInterface
    {
        if ($request->getStrategy() === static::RELEASE_GROUP) {
            return $this->releaseGroupStrategy;
        }

        return $this->composerUpdateStrategy;
    }
}
