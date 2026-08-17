<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Dto;

use DateTimeInterface;

class ReportMetadataDto
{
    protected string $organizationName;

    protected string $repositoryName;

    protected string $projectId;

    protected string $sourceCodeProvider;

    protected string $appEnv;

    protected string $reportId;

    protected int $idRg;

    protected DateTimeInterface $released;

    public function __construct(
        string $organizationName,
        string $repositoryName,
        string $projectId,
        string $sourceCodeProvider,
        string $appEnv,
        string $reportId,
        int $idRg,
        DateTimeInterface $released
    ) {
        $this->organizationName = $organizationName;
        $this->repositoryName = $repositoryName;
        $this->projectId = $projectId;
        $this->sourceCodeProvider = $sourceCodeProvider;
        $this->appEnv = $appEnv;
        $this->reportId = $reportId;
        $this->idRg = $idRg;
        $this->released = $released;
    }

    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getSourceCodeProvider(): string
    {
        return $this->sourceCodeProvider;
    }

    public function getAppEnv(): string
    {
        return $this->appEnv;
    }

    public function getReportId(): string
    {
        return $this->reportId;
    }

    public function getIdRg(): int
    {
        return $this->idRg;
    }

    public function getReleased(): DateTimeInterface
    {
        return $this->released;
    }
}
