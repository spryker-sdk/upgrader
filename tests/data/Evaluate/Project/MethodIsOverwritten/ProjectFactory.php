<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\MethodIsOverwritten;

use TestCore\MethodIsOverwritten\CoreFactory;
use TestCore\MethodIsOverwritten\FooPluginInterface;

class ProjectFactory extends CoreFactory
{
    /**
     * @return void
     */
    public function createTestModel(): void
    {
    }

    /**
     * @return void
     */
    public function createCoreModel(): void
    {
        parent::createCoreModel(); // TODO: Change the autogenerated stub
    }

    /**
     * @return \TestCore\MethodIsOverwritten\FooPluginInterface
     */
    protected function getCustomerStepHandler(): FooPluginInterface
    {
        return parent::getCustomerStepHandler();
    }
}