<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Infrastructure\IntegratorAdapter;

use PhpParser\ParserFactory;
use SprykerSdk\Integrator\ConfigReader\ConfigReader;
use SprykerSdk\Integrator\ConfigReader\ConfigReaderInterface;

class IntegratorConfigReaderFactory
{
    /**
     * @return \SprykerSdk\Integrator\ConfigReader\ConfigReaderInterface
     */
    public function createConfigReader(): ConfigReaderInterface
    {
        return new ConfigReader(new ParserFactory());
    }
}
