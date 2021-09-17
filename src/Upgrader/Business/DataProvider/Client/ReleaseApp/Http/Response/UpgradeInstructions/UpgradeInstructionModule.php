<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions;

class UpgradeInstructionModule
{
    public const TYPE_KEY = 'type';
    public const VERSION_KEY = 'version';

    /**
     * @var array
     */
    protected $bodyArray;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param array $bodyArray
     * @param string $name
     */
    public function __construct(array $bodyArray, string $name)
    {
        $this->bodyArray = $bodyArray;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->bodyArray[self::VERSION_KEY];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->bodyArray[self::TYPE_KEY];
    }
}
