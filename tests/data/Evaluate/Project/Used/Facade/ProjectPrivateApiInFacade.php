<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\Facade;

/**
 * @method \TestProject\Used\Facade\PrivateApi\ProjectEntityManager getEntityManager()
 * @method \TestProject\Used\Facade\PrivateApi\ProjectFactory getFactory()
 * @method \TestProject\Used\Facade\PrivateApi\ProjectRepository getRepository()
 */
class ProjectPrivateApiInFacade
{
    /**
     * @return void
     */
    public function testUsageProjectEntityManagerInFacadeWithoutError(): void
    {
        $this->getEntityManager()->save();
    }

    /**
     * @return void
     */
    public function testUsageProjectFactoryInFacadeWithoutError(): void
    {
        $this->getFactory()->createModel();
    }

    /**
     * @return void
     */
    public function testUsageProjectRepositoryInFacadeWithoutError(): void
    {
        $this->getRepository()->read();
    }
}
