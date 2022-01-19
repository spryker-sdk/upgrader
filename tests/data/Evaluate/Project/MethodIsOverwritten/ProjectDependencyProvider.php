<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\MethodIsOverwritten;

use TestCore\MethodIsOverwritten\CoreDependencyProvider;
use TestProject\MethodIsOverwritten\Plugin\Console\ProjectConsole;
use TestProject\MethodIsOverwritten\Plugin\EventSubscriber\ProjectEventSubscriber;

class ProjectDependencyProvider extends CoreDependencyProvider
{
    /**
     * @return void
     */
    public function getTestProjectMethod(): void
    {
    }

    /**
     * @return void
     */
    public function getCoreMethod(): void
    {
    }

    /**
     * @return array
     */
    public function getCoreDependencyProviderMethod(): array
    {
        $parent = parent::getCoreDependencyProviderMethod();

        return $parent + [];
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
        return [
            new ProjectConsole(),
        ];
    }

    /**
     * @return array
     */
    public function getEventSubscriber(): array
    {
        return [
            new ProjectEventSubscriber(),
        ];
    }
}
