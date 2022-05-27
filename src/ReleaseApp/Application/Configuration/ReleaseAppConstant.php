<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Application\Configuration;

class ReleaseAppConstant
{
    /**
     * @var string
     */
    public const RESPONSE_DATA_TIME_FORMAT = 'Y-m-d\TH:i:sP';

    /**
     * @var string
     */
    public const MODULE_TYPE_MAJOR = 'major';

    /**
     * @var string
     */
    public const MODULE_TYPE_MINOR = 'minor';

    /**
     * @var string
     */
    public const MODULE_TYPE_PATCH = 'patch';

    /**
     * @var string
     */
    public const RELEASE_GROUP_LINK_PATTERN = '%s/release-groups/view/%s';
}
