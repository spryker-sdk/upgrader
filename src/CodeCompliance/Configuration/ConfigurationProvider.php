<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Configuration;

class ConfigurationProvider
{
    /**
     * @var string
     */
    protected const DOCUMENTATION_BASE_URL = 'https://docs.spryker.com/docs/scos/dev/guidelines/keeping-a-project-upgradable/upgradability-guidelines/';

    /**
     * @return string
     */
    public function getDocumentationBaseUrl(): string
    {
        return static::DOCUMENTATION_BASE_URL;
    }
}
