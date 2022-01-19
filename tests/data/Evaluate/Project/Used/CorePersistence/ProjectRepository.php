<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\CorePersistence;

use TestCore\Used\CorePersistence\CoreRepository;

class ProjectRepository extends CoreRepository
{
    /**
     * @return void
     */
    public function testReadMethod()
    {
        $this->readFromCore();
    }

    /**
     * @return void
     */
    public function testCustomMethodWithoutCoreUsage(): void
    {
    }

    /**
     * @return void
     */
    public function testMethodWithUsageOfgetFactory(): void
    {
        $this->getFactory();
    }
}
