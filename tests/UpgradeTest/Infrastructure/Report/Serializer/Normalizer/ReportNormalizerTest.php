<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Infrastructure\Report\Serializer\Normalizer;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Upgrade\Domain\Entity\Package;
use Upgrade\Infrastructure\Report\Dto\ReportDto;
use Upgrade\Infrastructure\Report\Dto\ReportMetadataDto;
use Upgrade\Infrastructure\Report\Dto\ReportPayloadDto;
use Upgrade\Infrastructure\Report\Serializer\Normalizer\ReportNormalizer;

/**
 * @group UpgradeTest
 * @group Infrastructure
 * @group Report
 * @group Serializer
 * @group Normalizer
 * @group ReportNormalizerTest
 */
class ReportNormalizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testNormalizeShouldThrowExceptionWhenDataIsNotReportDto(): void
    {
        $this->expectException(InvalidArgumentException::class);
        // Arrange
        $reportDto = new stdClass();
        $reportJsonFormatter = new ReportNormalizer();

        // Act
        $reportJsonFormatter->normalize($reportDto);
    }

    /**
     * @return void
     */
    public function testNormalizeShouldReturnJsonArray(): void
    {
        // Arrange
        $reportDto = $this->createReportDto();
        $reportJsonFormatter = new ReportNormalizer();

        // Act
        $jsonArray = $reportJsonFormatter->normalize($reportDto);

        // Assert
        $this->assertSame([
            'name' => 'upgrader_report',
            'version' => 1,
            'scope' => 'Upgrader',
            'createdAt' => 1675326928,
            'payload' =>
                [
                    'required_packages' => [
                        [
                            'name' => 'spryker/category',
                            'version' => '1.0.1',
                            'previous_version' => '1.0.0',
                            'diff_link' => 'https://github.com/spryker/category/compare/1.0.0...1.0.1',
                        ],
                    ],
                    'dev_required_packages' => [
                        [
                            'name' => 'spryker/testify',
                            'version' => '2.0.1',
                            'previous_version' => '2.0.0',
                            'diff_link' => 'https://github.com/spryker/testify/compare/2.0.0...2.0.1',
                        ],
                    ],
                    'integrator_warnings' => [],
                ],
            'metadata' => [
                'organization_name' => 'spryker',
                'repository_name' => 'suite',
                'project_id' => '',
                'source_code_provider' => 'github',
                'application_env' => 'CI',
                'report_id' => '902072d8-a2cc-11ed-a8fc-0242ac120002',
            ],
        ], $jsonArray);
    }

    /**
     * @return \Upgrade\Infrastructure\Report\Dto\ReportDto
     */
    protected function createReportDto(): ReportDto
    {
        return new ReportDto(
            'upgrader_report',
            1,
            'Upgrader',
            (new DateTimeImmutable())->setTimestamp(1675326928),
            new ReportPayloadDto(
                [
                    new Package(
                        'spryker/category',
                        '1.0.1',
                        '1.0.0',
                        'https://github.com/spryker/category/compare/1.0.0...1.0.1',
                    ),
                ],
                [
                    new Package(
                        'spryker/testify',
                        '2.0.1',
                        '2.0.0',
                        'https://github.com/spryker/testify/compare/2.0.0...2.0.1',
                    ),
                ],
            ),
            new ReportMetadataDto(
                'spryker',
                'suite',
                '',
                'github',
                'CI',
                '902072d8-a2cc-11ed-a8fc-0242ac120002',
            ),
        );
    }
}
