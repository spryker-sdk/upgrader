<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities\UpgradeAnalysis\Request;

use ReleaseApp\Domain\Entities\RequestInterface;
use ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\UpgradeAnalysisResponse;

class UpgradeAnalysisRequest implements RequestInterface
{
    /**
     * @var string
     */
    protected const ENDPOINT = '/upgrade-analysis.json';

    /**
     * @var string
     */
    protected $projectName;

    /**
     * @var array
     */
    protected $composerJson;

    /**
     * @var array
     */
    protected $composerLock;

    /**
     * @param string $projectName
     * @param array $composerJson
     * @param array $composerLock
     */
    public function __construct(string $projectName, array $composerJson, array $composerLock)
    {
        $this->projectName = $projectName;
        $this->composerJson = $composerJson;
        $this->composerLock = $composerLock;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return static::ENDPOINT;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return static::REQUEST_TYPE_POST;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return (string)json_encode($this->getBodyArray());
    }

    /**
     * @return string
     */
    public function getResponseClass(): string
    {
        return UpgradeAnalysisResponse::class;
    }

    /**
     * @return array
     */
    protected function getBodyArray(): array
    {
        $composerJsonContent = json_encode($this->composerJson);
        $composerLockContent = json_encode($this->composerLock);

        return [
            'projectName' => $this->projectName,
            'composerJson' => $composerJsonContent,
            'composerLock' => $composerLockContent,
        ];
    }
}
