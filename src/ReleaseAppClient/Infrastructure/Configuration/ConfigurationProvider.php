<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Infrastructure\Configuration;

class ConfigurationProvider
{
    /**
     * @var string
     */
    protected const DEFAULT_RELEASE_APP_URL = 'https://api.release.spryker.com';

    /**
     * @var int
     */
    protected const DEFAULT_HTTP_RETRIEVE_ATTEMPTS_COUNT = 5;

    /**
     * @var int
     */
    protected const DEFAULT_HTTP_RETRIEVE_RETRY_DELAY = 10;

    /**
     * @return string
     */
    public function getReleaseAppUrl(): string
    {
        return (string)getenv('UPGRADER_RELEASE_APP_URL') ?: static::DEFAULT_RELEASE_APP_URL;
    }

    /**
     * @return int
     */
    public function getHttpRetrieveAttemptsCount(): int
    {
        return (int)getenv('UPGRADER_HTTP_RETRIEVE_ATTEMPTS_COUNT') ?: static::DEFAULT_HTTP_RETRIEVE_ATTEMPTS_COUNT;
    }

    /**
     * @return int
     */
    public function getHttpRetrieveRetryDelay(): int
    {
        return (int)getenv('UPGRADER_HTTP_RETRIEVE_RETRY_DELAY') ?: static::DEFAULT_HTTP_RETRIEVE_RETRY_DELAY;
    }
}
