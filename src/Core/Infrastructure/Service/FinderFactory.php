<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Core\Infrastructure\Service;

use Symfony\Component\Finder\Finder;

class FinderFactory
{
    /**
     * @return \Symfony\Component\Finder\Finder
     */
    public function createFinder(): Finder
    {
        return new Finder();
    }
}
