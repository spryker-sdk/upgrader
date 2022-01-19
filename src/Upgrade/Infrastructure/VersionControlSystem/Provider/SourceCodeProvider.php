<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Provider;

use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\SourceCodeProviderIsNotDefinedException;

class SourceCodeProvider
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected $configurationProvider;

    /**
     * @var array<\Upgrade\Infrastructure\VersionControlSystem\Provider\ProviderInterface>
     */
    protected $providers = [];

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param array<\Upgrade\Infrastructure\VersionControlSystem\Provider\ProviderInterface> $providers
     */
    public function __construct(ConfigurationProvider $configurationProvider, array $providers = [])
    {
        $this->configurationProvider = $configurationProvider;
        $this->providers = $providers;
    }

    /**
     * @thorws \Upgrade\Infrastructure\Exception\SourceCodeProviderIsNotDefinedException
     *
     * @throws \Upgrade\Infrastructure\Exception\SourceCodeProviderIsNotDefinedException
     *
     * @return \Upgrade\Infrastructure\VersionControlSystem\Provider\ProviderInterface
     */
    public function getSourceCodeProvider(): ProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->getName() === $this->configurationProvider->getSourceCodeProvider()) {
                return $provider;
            }
        }

        throw new SourceCodeProviderIsNotDefinedException();
    }
}
