<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CoreTest\Domain\ValueObject;

use Core\Domain\ValueObject\Id;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    /**
     * @return void
     */
    public function testUuidIsValid(): void
    {
        $id = new Id();

        $regex = '/^[a-f\d]{8}-[a-f\d]{4}-[a-f\d]{4}-[a-f\d]{4}-[a-f\d]{12}$/i';
        $this->assertMatchesRegularExpression($regex, (string)$id);
    }
}
