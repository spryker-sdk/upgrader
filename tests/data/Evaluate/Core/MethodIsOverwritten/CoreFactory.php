<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestCore\MethodIsOverwritten;

class CoreFactory
{
    /**
     * @return void
     */
    public function createCoreModel(): void
    {
    }

    /**
     * @return \TestCore\MethodIsOverwritten\FooPluginInterface
     */
    protected function getCustomerStepHandler(): FooPluginInterface
    {
        return new FooPlugin();
    }
}
