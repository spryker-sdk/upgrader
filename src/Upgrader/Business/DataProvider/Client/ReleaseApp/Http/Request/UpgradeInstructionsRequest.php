<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request;

use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionsResponse;

class UpgradeInstructionsRequest extends AbstractHttpRequest
{
    public const ENDPOINT = '/upgrade-instructions.json';

    /**
     * @var int
     */
    protected $moduleVersionId;

    /**
     * @param int $moduleVersionId
     */
    public function __construct(int $moduleVersionId)
    {
        $this->moduleVersionId = $moduleVersionId;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return sprintf('%s?%s', self::ENDPOINT, $this->getParametersAsString());
    }

    /**
     * @return string
     */
    protected function getParametersAsString(): string
    {
        return sprintf('%s=%s', 'module_version_id', $this->moduleVersionId);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return self::REQUEST_TYPE_GET;
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
        return UpgradeInstructionsResponse::class;
    }
}
