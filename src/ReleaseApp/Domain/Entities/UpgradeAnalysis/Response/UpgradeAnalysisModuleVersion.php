<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities\UpgradeAnalysis\Response;

use DateTime;
use DateTimeInterface;
use ReleaseApp\Application\Configuration\ReleaseAppConst;
use Upgrade\Infrastructure\Exception\UpgraderException;

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
    protected $bodyArray;

    /**
     * @param array $bodyArray
     */
    public function __construct(array $bodyArray)
    {
        $this->bodyArray = $bodyArray;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->bodyArray[static::ID_KEY];
    }

    /**
     * @return int
     */
    public function getName(): int
    {
        return $this->bodyArray[static::NAME_KEY];
    }

    /**
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return \DateTimeInterface
     */
    public function getCreated(): DateTimeInterface
    {
        $dataTime = DateTime::createFromFormat(
            ReleaseAppConst::RESPONSE_DATA_TIME_FORMAT,
            $this->bodyArray[static::CREATED_KEY],
        );

        if (!$dataTime) {
            $message = sprintf('%s %s', 'Invalid datatime format:', $this->bodyArray[static::CREATED_KEY]);

            throw new UpgraderException($message);
        }

        return $dataTime;
    }
}
