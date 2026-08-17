<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider;

use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Exception\SourceCodeProviderIsNotDefinedException;

class SourceCodeProvider
{
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var array<\Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\SourceCodeProviderInterface>
     */
    protected array $sourceCodeProviders = [];

    /**
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
