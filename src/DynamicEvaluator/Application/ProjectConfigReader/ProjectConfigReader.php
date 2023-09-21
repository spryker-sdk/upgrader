<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\ProjectConfigReader;

use Upgrader\Configuration\ConfigurationProvider;

class ProjectConfigReader implements ProjectConfigReaderInterface
{
    /**
     * @var string
     */
    protected const PROJECT_DEFAULT_CONFIG_PATH = 'config' . DIRECTORY_SEPARATOR . 'Shared' . DIRECTORY_SEPARATOR . 'config_default.php';

    /**
     * @var string
     */
    protected const PROJECT_NAMESPACES_CONFIG_KEY = 'KernelConstants::PROJECT_NAMESPACES';

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \DynamicEvaluator\Application\ProjectConfigReader\ConfigReaderInterface
     */
    protected ConfigReaderInterface $configReader;

    /**
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \DynamicEvaluator\Application\ProjectConfigReader\ConfigReaderInterface $configReader
     */
    public function __construct(ConfigurationProvider $configurationProvider, ConfigReaderInterface $configReader)
    {
        $this->configurationProvider = $configurationProvider;
        $this->configReader = $configReader;
    }

    /**
     * @return array<string>
     */
    public function getProjectNamespaces(): array
    {
        $configValues = $this->configReader->read(
            rtrim($this->configurationProvider->getRootPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . static::PROJECT_DEFAULT_CONFIG_PATH,
            [static::PROJECT_NAMESPACES_CONFIG_KEY],
        );

        return $configValues[static::PROJECT_NAMESPACES_CONFIG_KEY] ?? [];
    }
}
