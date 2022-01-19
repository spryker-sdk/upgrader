<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\Persistence;

/**
 * @method \TestCore\Used\Persistence\CustomerAccessPersistenceFactory getFacade()
 */
class WithoutFactoryInDocCommentRepository
{
    /**
     * @return void
     */
    public function methodWithCoreDependencyIncorrectBehavior(): void
    {
        $this->getFactory()->createCustomerAccessMapper();
    }
}
