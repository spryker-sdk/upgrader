<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\Exception;

use Exception;
use Throwable;

class ProjectConfigurationFileInvalidSyntaxException extends Exception
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'Invalid configuration file %s. %s';

    /**
     * @param string $configurationFile
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $configurationFile, string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(static::ERROR_MESSAGE, $configurationFile, $message), $code, $previous);
    }
}
