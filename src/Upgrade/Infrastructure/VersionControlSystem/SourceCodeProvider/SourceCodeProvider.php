<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider;

use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\SourceCodeProviderIsNotDefinedException;

class SourceCodeProvider
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var array<\Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface>
     */
    protected array $sourceCodeProviders = [];

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param array<\Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface> $providers
     */
    public function __construct(ConfigurationProvider $configurationProvider, array $providers = [])
    {
        $this->configurationProvider = $configurationProvider;
        $this->sourceCodeProviders = $providers;
    }

    /**
     * @thorws \Upgrade\Infrastructure\Exception\SourceCodeProviderIsNotDefinedException
     *
     * @throws \Upgrade\Infrastructure\Exception\SourceCodeProviderIsNotDefinedException
     *
     * @return \Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface
     */
    public function getSourceCodeProvider(): SourceCodeProviderInterface
    {
        foreach ($this->sourceCodeProviders as $provider) {
            if ($provider->getName() === $this->configurationProvider->getSourceCodeProvider()) {
                return $provider;
            }
        }

        throw new SourceCodeProviderIsNotDefinedException();
    }
}
