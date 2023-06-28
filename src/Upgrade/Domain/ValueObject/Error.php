<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Domain\ValueObject;

/**
 * @codeCoverageIgnore
 */
class Error implements ErrorInterface
{
    /**
     * @var string
     */
    public const CLIENT_CODE_ERROR = 'clientCodeError';

    /**
     * @var string
     */
    public const INTERNAL_ERROR = 'internalError';

    /**
     * @var string
     */
    protected string $errorMessage;

    /**
     * @var string
     */
    protected string $errorType;

    /**
     * @param string $errorMessage
     * @param string $errorType
     */
    public function __construct(string $errorMessage, string $errorType)
    {
        $this->errorMessage = $errorMessage;
        $this->errorType = $errorType;
    }

    /**
     * @param string $errorMessage
     *
     * @return self
     */
    public static function createClientCodeError(string $errorMessage): self
    {
        return new self($errorMessage, static::CLIENT_CODE_ERROR);
    }

    /**
     * @param string $errorMessage
     *
     * @return self
     */
    public static function createInternalError(string $errorMessage): self
    {
        return new self($errorMessage, static::INTERNAL_ERROR);
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getErrorType(): string
    {
        return $this->errorType;
    }
}
