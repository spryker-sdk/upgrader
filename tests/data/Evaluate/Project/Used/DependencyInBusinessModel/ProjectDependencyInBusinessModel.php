<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\DependencyInBusinessModel;

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
     * @param \TestCore\Used\DependencyInBusinessModel\CoreDependencyInBusinessModelFacade $coreDependencyInBusinessModelFacade
     * @param \TestCore\Used\DependencyInBusinessModel\CoreDependencyInBusinessModelRepository $coreDependencyInBusinessModelRepository
     */
    public function __construct(
        CoreDependencyInBusinessModelFacade $coreDependencyInBusinessModelFacade,
        CoreDependencyInBusinessModelRepository $coreDependencyInBusinessModelRepository
    ) {
        $this->coreDependencyInBusinessModelFacade = $coreDependencyInBusinessModelFacade;
        $this->coreDependencyInBusinessModelRepository = $coreDependencyInBusinessModelRepository;
    }
}
