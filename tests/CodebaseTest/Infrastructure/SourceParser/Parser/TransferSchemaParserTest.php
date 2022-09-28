<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodebaseTest\Infrastructure\SourceParser\Parser;

use Codebase\Infrastructure\SourceParser\FileParser\TransferSchemaParser;

class TransferSchemaParserTest extends BaseParser
{
    /**
     * @var string
     */
    protected const TRANSFER_SCHEMA_EXTENSION = 'transfer.xml';

    /**
     * @return void
     */
    public function testParse(): void
    {
        //Arrange
        $codebaseSourceDto = $this->createCodebaseSourceDto();

        //Act
        $codebaseSourceDto = $this->runParser(TransferSchemaParser::class, $codebaseSourceDto, static::TRANSFER_SCHEMA_EXTENSION);

        //Assert
        $codebaseTransferSchemaSource = $codebaseSourceDto->getTransferSchemaCodebaseSources();
        $codebaseTransferSchemaCoreSource = $codebaseSourceDto->getTransferSchemaCoreCodebaseSources();
        $this->assertNotEmpty($codebaseTransferSchemaSource);

        foreach ($codebaseSourceDto->getTransferSchemaCodebaseSources() as $transferCodebaseSource) {
            $this->assertInstanceOf('\Codebase\Application\Dto\XmlDto', $transferCodebaseSource);
        }

        $this->assertNotEmpty($codebaseTransferSchemaCoreSource);
    }
}
