<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions;

class UpgradeInstructionModule
{
    protected const TYPE_KEY = 'type';
    protected const VERSION_KEY = 'version';

    /**
     * @var array
     */
    protected $body;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param array $body
     * @param string $name
     */
    public function __construct(array $body, string $name)
    {
        $this->body = $body;
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
        return $this->body[static::VERSION_KEY];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->body[static::TYPE_KEY];
    }
}
