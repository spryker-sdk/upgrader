<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodebaseTest\Infrastructure\SourceParser\Parser;

use Codebase\Infrastructure\SourceParser\Parser\DatabaseSchemaParser;

class DatabaseSchemaParserTest extends BaseParser
{
    /**
     * @var string
     */
    protected const DATABASE_SCHEMA_EXTENSION = 'schema.xml';

    /**
     * @return void
     */
    public function testParse(): void
    {
        //Arrange
        $codebaseSourceDto = $this->createCodebaseSourceDto();

        //Act
        $codebaseSourceDto = $this->runParser(DatabaseSchemaParser::class, $codebaseSourceDto, static::DATABASE_SCHEMA_EXTENSION);

        //Assert
        $codebaseDatabaseSchemaSource = $codebaseSourceDto->getDatabaseSchemaCodebaseSources();
        $codebaseDatabaseSchemaCoreSource = $codebaseSourceDto->getDatabaseSchemaCoreCodebaseSources();
        $this->assertNotEmpty($codebaseDatabaseSchemaSource);

        foreach ($codebaseSourceDto->getDatabaseSchemaCodebaseSources() as $databaseSchemaCodebaseSource) {
            $this->assertInstanceOf('\Codebase\Application\Dto\XmlDto', $databaseSchemaCodebaseSource);
        }

        $this->assertNotEmpty($codebaseDatabaseSchemaCoreSource);
    }
}
