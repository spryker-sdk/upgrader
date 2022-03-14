<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

interface Filters
{
    /**
     * @var string
     */
    public const BUSINESS_FACTORY_FILTER = 'BUSINESS_FACTORY_FILTER';

    /**
     * @var string
     */
    public const BUSINESS_MODEL_FILTER = 'BUSINESS_MODEL_FILTER';

    /**
     * @var string
     */
    public const FACADE_FILTER = 'FACADE_FILTER';

    /**
     * @var string
     */
    public const PRIVATE_API_FILTER = 'PRIVATE_API_FILTER';

    /**
     * @var string
     */
    public const PERSISTENCE_FILTER = 'PERSISTENCE_FILTER';

    /**
     * @var string
     */
    public const CORE_EXTENSION_FILTER = 'CORE_EXTENSION_FILTER';

    /**
     * @var string
     */
    public const IGNORE_LIST_FILTER = 'IGNORE_LIST_FILTER';

    /**
     * @var string
     */
    public const PLUGIN_FILTER = 'PLUGIN_FILTER';
}
