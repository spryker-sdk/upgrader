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
    /**
     * @var string
     */
    protected string $organizationName;

    /**
     * @var string
     */
    protected string $repositoryName;

    /**
     * @var string
     */
    protected string $projectId;

    /**
     * @var string
     */
    protected string $sourceCodeProvider;

    /**
     * @var string
     */
    protected string $appEnv;

    /**
     * @var string
     */
    protected string $reportId;

    /**
     * @var int
     */
    protected int $idRg;

    /**
     * @var \DateTimeInterface
     */
    protected DateTimeInterface $released;

    /**
     * @param string $organizationName
     * @param string $repositoryName
     * @param string $projectId
     * @param string $sourceCodeProvider
     * @param string $appEnv
     * @param string $reportId
     * @param int $idRg
     * @param \DateTimeInterface $released
     */
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

    /**
     * @return string
     */
    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }

    /**
     * @return string
     */
    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    /**
     * @return string
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * @return string
     */
    public function getSourceCodeProvider(): string
    {
        return $this->sourceCodeProvider;
    }

    /**
     * @return string
     */
    public function getAppEnv(): string
    {
        return $this->appEnv;
    }

    /**
     * @return string
     */
    public function getReportId(): string
    {
        return $this->reportId;
    }

    /**
     * @return int
     */
    public function getIdRg(): int
    {
        return $this->idRg;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getReleased(): DateTimeInterface
    {
        return $this->released;
    }
}
