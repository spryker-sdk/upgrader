<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeInstructions\Request;

use ReleaseAppClient\Domain\Http\HttpRequestInterface;
use ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsResponse;

class HttpUpgradeInstructionsRequest implements HttpRequestInterface
{
    /**
     * @var string
     */
    protected const ENDPOINT = '/upgrade-instructions.json';

    /**
     * @var int
     */
    protected $idModuleVersion;

    /**
     * @param int $moduleVersionId
     */
    public function __construct(int $moduleVersionId)
    {
        $this->idModuleVersion = $moduleVersionId;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return sprintf('%s?%s', static::ENDPOINT, $this->getParametersAsString());
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return static::REQUEST_TYPE_GET;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getResponseClass(): string
    {
        return HttpUpgradeInstructionsResponse::class;
    }

    /**
     * @return string
     */
    protected function getParametersAsString(): string
    {
        return sprintf('%s=%s', 'module_version_id', $this->idModuleVersion);
    }
}