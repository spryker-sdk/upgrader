<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common;

use Upgrade\Application\Provider\ConfigurationProviderInterface;

class IntegratorExecutionValidator implements IntegratorExecutionValidatorInterface
{
    /**
     * @var int
     */
    protected const MAX_MANIFESTS_RATING_THRESHOLD = 100;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(ConfigurationProviderInterface $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return bool
     */
    public function isIntegratorShouldBeInvoked(): bool
    {
        return $this->configurationProvider->isIntegratorEnabled()
            && $this->configurationProvider->getManifestsRatingThreshold() <= static::MAX_MANIFESTS_RATING_THRESHOLD;
    }
}
