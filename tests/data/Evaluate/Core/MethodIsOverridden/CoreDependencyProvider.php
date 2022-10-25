<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestCore\MethodIsOverridden;

class CoreDependencyProvider
{
    /**
     * @return void
     */
    public function getCoreMethod(): void
    {
    }

    /**
     * @return array
     */
    public function getPlugins(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getConsoleCommands(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getEventSubscriber(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getCoreDependencyProviderMethod(): array
    {
        return [];
    }
}
