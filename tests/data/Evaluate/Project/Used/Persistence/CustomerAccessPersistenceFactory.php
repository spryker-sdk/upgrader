<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\Persistence;

use TestCore\Used\Persistence\CustomerAccessPersistenceFactory as CoreCustomerAccessPersistenceFactory;

class CustomerAccessPersistenceFactory extends CoreCustomerAccessPersistenceFactory
{
    /**
     * @return mixed
     */
    public function createCustomerAccessBuilder(): mixed
    {
        return null;
    }
}
