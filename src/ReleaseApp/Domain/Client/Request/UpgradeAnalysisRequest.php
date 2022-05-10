<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Client\Request;

use ReleaseApp\Domain\Entities\UpgradeAnalysis;

class UpgradeAnalysisRequest implements RequestInterface
{
    /**
     * @var string
     */
    protected string $projectName;

    /**
     * @var array
     */
    protected array $composerJson;

    /**
     * @var array
     */
    protected array $composerLock;

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
    public function getBody(): string
    {
        return (string)json_encode($this->getBodyArray());
    }

    /**
     * @return string
     */
    public function getResponseClass(): string
    {
        return UpgradeAnalysis::class;
    }

    /**
     * @return string|null
     */
    public function getParameters(): ?string
    {
        return null;
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
