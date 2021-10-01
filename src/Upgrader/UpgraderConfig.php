<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader;

class UpgraderConfig
{
    protected const UPGRADER_RELEASE_APP_URL = 'UPGRADER_RELEASE_APP_URL';
    protected const DEFAULT_RELEASE_APP_URL = 'https://api.release.spryker.com';
    protected const UPGRADER_COMMAND_EXECUTION_TIMEOUT = 'UPGRADER_COMMAND_EXECUTION_TIMEOUT';
    protected const DEFAULT_COMMAND_EXECUTION_TIMEOUT = 600;

    /**
     * @var string|null
     */
    protected $previousCommitHash;

    /**
     * @return int
     */
    public function getCommandExecutionTimeout(): int
    {
        return (int)getenv(static::UPGRADER_COMMAND_EXECUTION_TIMEOUT) ?: static::DEFAULT_COMMAND_EXECUTION_TIMEOUT;
    }

    /**
     * @return string
     */
    public function getReleaseAppUrl(): string
    {
        return (string)getenv(static::UPGRADER_RELEASE_APP_URL) ?: static::DEFAULT_RELEASE_APP_URL;
    }
}
