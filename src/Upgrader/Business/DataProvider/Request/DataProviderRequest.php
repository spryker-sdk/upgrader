<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Request;

class DataProviderRequest implements DataProviderRequestInterface
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
