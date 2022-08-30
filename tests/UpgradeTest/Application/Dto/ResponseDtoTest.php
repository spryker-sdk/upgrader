<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeTest\Application\Dto;

use Generator;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\ResponseDto;

class ResponseDtoTest extends TestCase
{
    /**
     * @dataProvider gettersDataProvider
     *
     * @param bool $isSuccessful
     * @param string|null $outputMessage
     *
     * @return void
     */
    public function testGetters(bool $isSuccessful, ?string $outputMessage): void
    {
        $dto = new ResponseDto($isSuccessful, $outputMessage);

        $this->assertSame($isSuccessful, $dto->isSuccessful());
        $this->assertSame($outputMessage, $dto->getOutputMessage());
    }

    /**
     * @return \Generator
     */
    public function gettersDataProvider(): Generator
    {
        $dataProvider = [
            [true, null],
            [false, null],
            [true, ''],
            [false, 'Test output message'],
        ];

        foreach ($dataProvider as $set) {
            yield $set;
        }
    }
}
