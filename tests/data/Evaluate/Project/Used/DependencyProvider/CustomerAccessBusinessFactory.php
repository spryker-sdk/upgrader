<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\DependencyProvider;

use TestCore\Used\DependencyProvider\CustomerAccessBusinessFactory as CoreCustomerAccessBusinessFactory;

class CustomerAccessBusinessFactory extends CoreCustomerAccessBusinessFactory
{
    /**
     * @var string
     */
    protected const TEST_WITH_PREFIX_CONSTANT_NAME_IN_FACTORY = 'TEST_WITH_PREFIX_CONSTANT_NAME_IN_FACTORY';

    /**
     * @return mixed
     */
    public function getterWithoutCoreDependencyCorrectBehavior()
    {
        return $this->getProvidedDependency(ProductPageSearchDependencyProvider::TEST_WITH_PREFIX_CONSTANT_NAME);
    }

    /**
     * @return mixed
     */
    public function getterWithCoreDependencyIncorrectBehavior()
    {
        return $this->getProvidedDependency(ProductPageSearchDependencyProvider::QUERY_CONTAINER_CATEGORY);
    }

    /**
     * @return mixed
     */
    public function getterWithSelfDependencyIncorrectBehavior()
    {
        return $this->getProvidedDependency(self::TEST_WITH_PREFIX_CONSTANT_NAME_IN_FACTORY);
    }

    /**
     * @return mixed
     */
    public function getterWithStaticDependencyIncorrectBehavior()
    {
        return $this->getProvidedDependency(static::TEST_WITH_PREFIX_CONSTANT_NAME_IN_FACTORY);
    }

    /**
     * @return mixed
     */
    public function getterWithoutDependencyIncorrectBehavior()
    {
        return $this->getProvidedDependency('QUERY_CONTAINER_CATEGORY');
    }
}
