<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis;

use DateTime;
use DateTimeInterface;
use Upgrader\Business\DataProvider\Client\ReleaseApp\ReleaseAppConst;

class UpgradeAnalysisModuleVersion
{
    public const ID_KEY = 'id';
    public const NAME_KEY = 'name';
    public const CREATED_KEY = 'created';

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
        return $this->bodyArray[self::ID_KEY];
    }

    /**
     * @return int
     */
    public function getName(): int
    {
        return $this->bodyArray[self::NAME_KEY];
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreated(): DateTimeInterface
    {
        return DateTime::createFromFormat(
            ReleaseAppConst::RESPONSE_DATA_TIME_FORMAT,
            $this->bodyArray[self::CREATED_KEY]
        );
    }
}
