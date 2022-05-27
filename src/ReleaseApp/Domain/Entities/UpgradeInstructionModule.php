<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities;

use Upgrade\Application\Exception\UpgraderException;

class UpgradeInstructionModule
{
    /**
     * @var string
     */
    protected const TYPE_KEY = 'type';

    /**
     * @var string
     */
    protected const VERSION_KEY = 'version';

    /**
     * @var array<mixed>
     */
    protected array $body;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @param array<mixed> $body
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
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return string
     */
    public function getVersion(): string
    {
        if (!array_key_exists(static::VERSION_KEY, $this->body)) {
            throw new UpgraderException(sprintf('Key %s not found', static::VERSION_KEY));
        }

        return $this->body[static::VERSION_KEY];
    }

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return string
     */
    public function getType(): string
    {
        if (!array_key_exists(static::TYPE_KEY, $this->body)) {
            throw new UpgraderException(sprintf('Key %s not found', static::TYPE_KEY));
        }

        return $this->body[static::TYPE_KEY];
    }
}
