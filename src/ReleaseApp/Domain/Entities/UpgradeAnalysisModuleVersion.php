<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities;

use DateTime;
use DateTimeInterface;
use ReleaseApp\Application\Configuration\ReleaseAppConst;
use Upgrade\Application\Exception\UpgraderException;

class UpgradeAnalysisModuleVersion
{
    /**
     * @var string
     */
    protected const ID_KEY = 'id';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var string
     */
    protected const CREATED_KEY = 'created';

    /**
     * @var array
     */
    protected array $body;

    /**
     * @param array $bodyArray
     */
    public function __construct(array $bodyArray)
    {
        $this->body = $bodyArray;
    }

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return int
     */
    public function getId(): int
    {
        if (!array_key_exists(static::ID_KEY, $this->body)) {
            throw new UpgraderException('Key ' . static::ID_KEY . ' not found');
        }

        return $this->body[static::ID_KEY];
    }

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return int
     */
    public function getName(): int
    {
        if (!array_key_exists(static::NAME_KEY, $this->body)) {
            throw new UpgraderException('Key ' . static::NAME_KEY . ' not found');
        }

        return $this->body[static::NAME_KEY];
    }

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return \DateTimeInterface
     */
    public function getCreated(): DateTimeInterface
    {
        $dataTime = DateTime::createFromFormat(
            ReleaseAppConst::RESPONSE_DATA_TIME_FORMAT,
            $this->body[static::CREATED_KEY],
        );

        if (!$dataTime) {
            $message = sprintf('%s %s', 'Invalid datatime format:', $this->body[static::CREATED_KEY]);

            throw new UpgraderException($message);
        }

        return $dataTime;
    }
}
