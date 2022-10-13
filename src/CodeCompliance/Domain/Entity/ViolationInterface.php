<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Entity;

use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface as SdkContractsViolationInterface;

interface ViolationInterface extends SdkContractsViolationInterface, ArrayableInterface
{

}
