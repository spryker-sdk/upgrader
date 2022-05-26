<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\ToolingConfigurationReader\Validator;

interface ToolingConfigurationValidatorInterface
{
    /**
     * @param array<mixed> $configuration
     *
     * @return void
     */
    public function validate(array $configuration): void;
}
