<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class UpgraderExtension extends Extension
{
    /**
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configurationFiles = array_merge(
            glob(__DIR__ . '/../../src/*/*/DependencyInjection/config/'),
            glob(__DIR__ . '/../../src/*/DependencyInjection/config/')
        );

        if (!$configurationFiles) {
            return;
        }

        foreach ($configurationFiles as $configurationFile) {
            (new YamlFileLoader($container, new FileLocator($configurationFile)))->load('services.yml');
        }
    }
}
