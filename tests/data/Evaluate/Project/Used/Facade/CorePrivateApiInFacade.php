<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\Facade;

/**
 * @method \TestCore\Used\Facade\CoreEntityManager getEntityManager()
 * @method \TestCore\Used\Facade\CoreFactory getFactory()
 * @method \TestCore\Used\Facade\CoreRepository getRepository()
 */
class CorePrivateApiInFacade
{
 /**
  * @return void
  */
    public function testUsageCoreEntityManagerInFacadeWithError(): void
    {
        $this->getEntityManager()->save();
    }

    /**
     * @return void
     */
    public function testUsageCoreFactoryInFacadeWithError(): void
    {
        $this->getFactory()->createModel();
    }

    /**
     * @return void
     */
    public function testUsageCoreRepositoryInFacadeWithError(): void
    {
        $this->getRepository()->read();
    }
}
