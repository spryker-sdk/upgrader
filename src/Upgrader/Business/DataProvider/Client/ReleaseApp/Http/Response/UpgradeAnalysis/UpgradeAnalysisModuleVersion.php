<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis;

use DateTime;
use DateTimeInterface;
use Upgrader\Business\DataProvider\Client\ReleaseApp\ReleaseAppConst;
use Upgrader\Business\Exception\UpgraderException;

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
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return \DateTimeInterface
     */
    public function getCreated(): DateTimeInterface
    {
        $dataTime = DateTime::createFromFormat(
            ReleaseAppConst::RESPONSE_DATA_TIME_FORMAT,
            $this->bodyArray[self::CREATED_KEY]
        );

        if (!$dataTime) {
            $message = sprintf('%s %s', 'Invalid datatime format:', $this->bodyArray[self::CREATED_KEY]);

            throw new UpgraderException($message);
        }

        return $dataTime;
    }
}
