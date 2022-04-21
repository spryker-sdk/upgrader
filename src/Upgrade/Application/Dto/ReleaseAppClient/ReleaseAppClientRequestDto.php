<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\ReleaseAppClient;

class ReleaseAppClientRequestDto
{
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
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * @return array
     */
    public function getComposerJson(): array
    {
        return $this->composerJson;
    }

    /**
     * @return array
     */
    public function getComposerLock(): array
    {
        return $this->composerLock;
    }
}
