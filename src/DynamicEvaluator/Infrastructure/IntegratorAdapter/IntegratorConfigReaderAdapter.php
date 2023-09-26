<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Infrastructure\IntegratorAdapter;

use DynamicEvaluator\Application\ProjectConfigReader\ConfigReaderInterface;
use SprykerSdk\Integrator\ConfigReader\ConfigReaderInterface as IntegratorConfigReaderInterface;

class IntegratorConfigReaderAdapter implements ConfigReaderInterface
{
    /**
     * @var \SprykerSdk\Integrator\ConfigReader\ConfigReaderInterface
     */
    protected IntegratorConfigReaderInterface $configReader;

    /**
     * @param \SprykerSdk\Integrator\ConfigReader\ConfigReaderInterface $configReader
     */
    public function __construct(IntegratorConfigReaderInterface $configReader)
    {
        $this->configReader = $configReader;
    }

    /**
     * @param string $configPath
     * @param array<string> $configKeys
     *
     * @return array<string, mixed>
     */
    public function read(string $configPath, array $configKeys): array
    {
        return $this->configReader->read($configPath, $configKeys);
    }
}
