<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgraderTest\Report;

use CodeCompliance\Domain\Entity\Report;
use CodeCompliance\Domain\Entity\ReportInterface;
use CodeCompliance\Domain\Entity\Violation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;
use Upgrader\Report\Service\ReportService;

class ReportServiceTest extends KernelTestCase
{
    /**
     * @var string
     */
    protected const TEST_REPORT_PATH = 'tests/data/Evaluate/Project/report/report.yml';

    /**
     * @var string
     */
    protected const EXAMPLE_REPORT_PATH = 'tests/data/Evaluate/Project/report/report-example.yml';

    /**
     * @return void
     */
    public function testSaveReport(): void
    {
        //Arrange
        $reportService = $this->createPartialMock(ReportService::class, ['getReportPath']);
        $reportService->method('getReportPath')->willReturn(self::TEST_REPORT_PATH);
        $report = $this->getReport();

        //Act
        $reportService->saveReport($report);

        //Assert
        $this->assertSame(
            file_get_contents(static::TEST_REPORT_PATH),
            file_get_contents(static::EXAMPLE_REPORT_PATH),
        );

        unlink(self::TEST_REPORT_PATH);
    }

    /**
     * @return void
     */
    public function testGetReport(): void
    {
        //Arrange
        $reportService = $this->createPartialMock(ReportService::class, ['getReportPath']);
        $reportService->method('getReportPath')->willReturn(self::EXAMPLE_REPORT_PATH);

        //Act
        $deserializedReport = $reportService->getReport();

        //Assert
        $this->assertSame($deserializedReport->toArray(), $this->getReport()->toArray());
    }

    /**
     * @return void
     */
    public function testGenerateMessages(): void
    {
        //Arrange
        $reportService = new ReportService(new Yaml());

        //Act
        $messages = $reportService->generateMessages($this->getReport());

        //Assert
        $this->assertSame($messages, [
            'violation1_producedBy violation1_message
--------------------- ----------------------------------------------------------------------------------------------------',
            '<error>violation2_producedBy violation2_message</error>
--------------------- ----------------------------------------------------------------------------------------------------',
            'Total messages: 2',
        ]);
    }

    /**
     * @return void
     */
    public function testGenerateMessagesVerbosity(): void
    {
        //Arrange
        $reportService = new ReportService(new Yaml());

        //Act
        $messages = $reportService->generateMessages($this->getReport(), true);

        //Assert
        $this->assertSame($messages, [
            'violation1_producedBy violation1_message
                      💡More information: http://documentation1.html
--------------------- ----------------------------------------------------------------------------------------------------',
            '<error>violation2_producedBy violation2_message</error>
                      💡More information: http://documentation2.html
--------------------- ----------------------------------------------------------------------------------------------------',
            'Total messages: 2',
        ]);
    }

    /**
     * @return \CodeCompliance\Domain\Entity\ReportInterface
     */
    protected function getReport(): ReportInterface
    {
        $violation1 = new Violation(
            'id1',
            'violation1_message',
            'violation1_producedBy',
            'WARNING',
            ['documentation' => 'http://documentation1.html'],
        );
        $violation2 = new Violation(
            'id2',
            'violation2_message',
            'violation2_producedBy',
            'ERROR',
            ['documentation' => 'http://documentation2.html'],
        );

        $report = new Report('TestProject', 'test/path');
        $report->addViolations([$violation1, $violation2]);

        return $report;
    }
}
