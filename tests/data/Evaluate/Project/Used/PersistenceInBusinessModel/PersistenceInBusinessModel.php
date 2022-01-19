<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\PersistenceInBusinessModel;

use TestCore\Used\PersistenceInBusinessModel\CoreEntityManager;

class PersistenceInBusinessModel
{
    /**
     * @var \TestProject\Used\PersistenceInBusinessModel\BusinessModeRepository
     */
    protected BusinessModeRepository $projectRepository;

    /**
     * @var \TestCore\Used\PersistenceInBusinessModel\CoreEntityManager
     */
    protected CoreEntityManager $coreEntityManager;

    /**
     * @param \TestProject\Used\PersistenceInBusinessModel\BusinessModeRepository $projectRepository
     * @param \TestCore\Used\PersistenceInBusinessModel\CoreEntityManager $coreEntityManager
     */
    public function __construct(BusinessModeRepository $projectRepository, CoreEntityManager $coreEntityManager)
    {
        $this->projectRepository = $projectRepository;
        $this->coreEntityManager = $coreEntityManager;
    }

    /**
     * @return void
     */
    public function testSomeMethodToExecuteWithoutError(): void
    {
        $this->projectRepository->testCustomMethodWithoutCoreUsage();
    }

    /**
     * @return void
     */
    public function testSomeMethodToExecuteWithError(): void
    {
        $this->projectRepository->readFromCore();
    }

    /**
     * @return void
     */
    public function testSomeMethodUsedCoreEntityManagerWithError(): void
    {
        $this->coreEntityManager->save();
    }
}
