<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CoreTest\App\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class TestKernel extends BaseKernel
{
    /**
     * @return array
     */
    public function registerBundles()
    {
        return [];
    }

    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     *
     * @return void
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $configurationFiles = glob(APPLICATION_ROOT_DIR . '/src/*/*/DependencyInjection/config/*.yml');

        foreach ($configurationFiles as $configurationFile) {
            $loader->load($configurationFile);
        }
    }
}
