<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser;

use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParser;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Utils\Infrastructure\Service\Filesystem;

class XmlSchemaFileParserTest extends TestCase
{
    /**
     * @return void
     */
    public function testParseXmlToColumnsMapShouldReturnEmptyArrayWhenFileDoesNotExist(): void
    {
        // Arrange & Assert
        $xmlSchemaFileParser = new XmlSchemaFileParser(
            $this->createFilesystemMock('', false),
        );

        // Act
        $result = $xmlSchemaFileParser->parseXmlToColumnsMap('schema.xml');

        // Assert
        $this->assertEmpty($result);
    }

    /**
     * @return void
     */
    public function testParseXmlToColumnsMapShouldParseXmlColumns(): void
    {
        //Arrange & Assert
        $xmlSchemaFileParser = new XmlSchemaFileParser(
            $this->createFilesystemMock(
                <<<SCHEMA
                <database xmlns="spryker:schema-01" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" name="zed" xsi:schemaLocation="spryker:schema-01 https://static.spryker.com/schema-01.xsd" namespace="Orm\Zed\ApiKey\Persistence" package="src.Orm.Zed.ApiKey.Persistence">
                    <non-table-node></non-table-node>
                    <table name="spy_api_key" idMethod="native" identifierQuoting="true">
                        <column name="id_api_key" required="true" type="INTEGER" autoIncrement="true" primaryKey="true"/>
                        <column name="name" required="true" size="255" type="VARCHAR"/>
                        <id-method-parameter value="spy_api_key_pk_seq"/>
                        <unique name="spy_api_key-name">
                            <unique-column name="name"/>
                        </unique>
                        <unique name="spy_api_key-key_hash">
                            <unique-column name="key_hash"/>
                        </unique>

                         <foreign-key name="spy_api_key-created_by" foreignTable="spy_user" phpName="User" refPhpName="ApiKey">
                            <reference local="created_by" foreign="id_user"/>
                        </foreign-key>
                        <behavior name="timestampable"/>
                    </table>

                </database>
                SCHEMA,
            ),
        );

        // Act
        $result = $xmlSchemaFileParser->parseXmlToColumnsMap('schema.xml');

        // Assert
        $this->assertSame(['spy_api_key' => ['id_api_key', 'name']], $result);
    }

    /**
     * @param string $fileContent
     * @param bool $fileExists
     *
     * @return \SprykerSdk\Utils\Infrastructure\Service\Filesystem
     */
    protected function createFilesystemMock(string $fileContent, bool $fileExists = true): Filesystem
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('readFile')->willReturn($fileContent);
        $filesystem->method('exists')->willReturn($fileExists);

        return $filesystem;
    }
}
