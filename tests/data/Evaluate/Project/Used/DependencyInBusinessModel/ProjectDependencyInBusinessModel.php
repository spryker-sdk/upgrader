<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\DependencyInBusinessModel;

use PHPUnit\Framework\TestCase;
use TestCore\Used\DependencyInBusinessModel\CoreDependencyInBusinessModelFacade;
use TestCore\Used\DependencyInBusinessModel\CoreDependencyInBusinessModelRepository;

class ProjectDependencyInBusinessModel
{
    /**
     * @var \TestCore\Used\DependencyInBusinessModel\CoreDependencyInBusinessModelFacade
     */
    protected CoreDependencyInBusinessModelFacade $coreDependencyInBusinessModelFacade;

    /**
     * @var \TestCore\Used\DependencyInBusinessModel\CoreDependencyInBusinessModelRepository
     */
    protected CoreDependencyInBusinessModelRepository $coreDependencyInBusinessModelRepository;

    /**
     * @var \PHPUnit\Framework\TestCase $testCase
     */
    protected TestCase $testCase;

    /**
     * @param \TestCore\Used\DependencyInBusinessModel\CoreDependencyInBusinessModelFacade $coreDependencyInBusinessModelFacade
     * @param \TestCore\Used\DependencyInBusinessModel\CoreDependencyInBusinessModelRepository $coreDependencyInBusinessModelRepository
     * @param \PHPUnit\Framework\TestCase $testCase
     */
    public function __construct(
        CoreDependencyInBusinessModelFacade $coreDependencyInBusinessModelFacade,
        CoreDependencyInBusinessModelRepository $coreDependencyInBusinessModelRepository,
        TestCase $testCase
    ) {
        $this->coreDependencyInBusinessModelFacade = $coreDependencyInBusinessModelFacade;
        $this->coreDependencyInBusinessModelRepository = $coreDependencyInBusinessModelRepository;
        $this->testCase = $testCase;
    }
}
