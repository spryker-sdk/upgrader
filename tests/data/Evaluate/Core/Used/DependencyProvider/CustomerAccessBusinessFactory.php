<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestCore\Used\DependencyProvider;

class CustomerAccessBusinessFactory
{
    /**
     * @return mixed
     */
    public function getterInCoreCorrectBehavior()
    {
        return $this->getProvidedDependency(ProductPageSearchDependencyProvider::QUERY_CONTAINER_CATEGORY);
    }

    /**
     * @param string $key
     *
     * @return null
     */
    public function getProvidedDependency($key)
    {
        return null;
    }
}
