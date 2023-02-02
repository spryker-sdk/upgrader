<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\ReportFormatter;

use Upgrade\Domain\Entity\Package;
use Upgrade\Infrastructure\Report\Dto\ReportDto;
use Upgrade\Infrastructure\Report\Dto\ReportMetadataDto;
use Upgrade\Infrastructure\Report\Dto\ReportPayloadDto;

class ReportJsonFormatter implements ReportFormatterInterface
{
    /**
     * @param \Upgrade\Infrastructure\Report\Dto\ReportDto $reportDto
     *
     * @return array<string, mixed>
     */
    public function format(ReportDto $reportDto): array
    {
        return [
            'name' => $reportDto->getName(),
            'version' => $reportDto->getVersion(),
            'scope' => $reportDto->getScope(),
            'createdAt' => $reportDto->getCreatedAt()->getTimestamp(),
            'payload' => $this->formatPayload($reportDto->getPayload()),
            'metadata' => $this->formatMetaData($reportDto->getMetadata()),
        ];
    }

    /**
     * @param \Upgrade\Infrastructure\Report\Dto\ReportPayloadDto $reportPayloadDto
     *
     * @return array<mixed, array<array<string, mixed>>>
     */
    protected function formatPayload(ReportPayloadDto $reportPayloadDto): array
    {
        return [
            'required_packages' => array_map([$this, 'formatPackage'], $reportPayloadDto->getRequiredPackages()),
            'dev_required_packages' => array_map([$this, 'formatPackage'], $reportPayloadDto->getDevRequiredPackages()),
        ];
    }

    /**
     * @param \Upgrade\Infrastructure\Report\Dto\ReportMetadataDto $metadataDto
     *
     * @return array<string, mixed>
     */
    protected function formatMetaData(ReportMetadataDto $metadataDto): array
    {
        return [
            'organization_name' => $metadataDto->getOrganizationName(),
            'repository_name' => $metadataDto->getRepositoryName(),
            'gitlab_project_id' => $metadataDto->getGitLabProjectId(),
            'source_code_provider' => $metadataDto->getSourceCodeProvider(),
            'execution_env' => $metadataDto->getExecutionEnv(),
            'report_id' => $metadataDto->getReportId(),
        ];
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return array<string, mixed>
     */
    protected function formatPackage(Package $package): array
    {
        return [
            'name' => $package->getName(),
            'version' => $package->getVersion(),
            'previous_version' => $package->getPreviousVersion(),
            'diff_link' => $package->getDiffLink(),
        ];
    }
}
