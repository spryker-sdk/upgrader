<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Exception;

use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;

class UpgraderFlowException extends UpgraderException
{
    /**
     * @var string
     */
    protected const UPGRADED_FLOW_EXCEPTION = 'Upgraded flow exception';

    /**
     * @var \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    protected $responses;

    /**
     * @param \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection $responses
     */
    public function __construct(UpgraderResponseCollection $responses)
    {
        parent::__construct(self::UPGRADED_FLOW_EXCEPTION);
        $this->responses = $responses;
    }

    /**
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    public function getResponses(): UpgraderResponseCollection
    {
        return $this->responses;
    }
}
