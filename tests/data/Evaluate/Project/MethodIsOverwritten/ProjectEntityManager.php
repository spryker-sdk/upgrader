<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\MethodIsOverwritten;

use TestCore\MethodIsOverwritten\CoreEntityManager;

class ProjectEntityManager extends CoreEntityManager
{
    /**
     * @return void
     */
    public function getTestSave(): void
    {
    }

    /**
     * @return void
     */
    public function save(): void
    {
        parent::save();
    }
}
