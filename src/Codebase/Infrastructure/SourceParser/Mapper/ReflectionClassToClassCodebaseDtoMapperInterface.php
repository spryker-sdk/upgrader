<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\Mapper;

use Codebase\Application\Dto\ClassCodebaseDto;
use ReflectionClass;

interface ReflectionClassToClassCodebaseDtoMapperInterface
{
    public function map(ReflectionClass $reflectionClass, array $projectPrefixes, array $coreNamespaces = []): ClassCodebaseDto;
}
