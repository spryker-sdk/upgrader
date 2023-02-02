<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Dto;

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
    protected string $gitLabProjectId;

    /**
     * @var string
     */
    protected string $sourceCodeProvider;

    /**
     * @var string
     */
    protected string $executionEnv;

    /**
     * @var string
     */
    protected string $reportId;

    /**
     * @param string $organizationName
     * @param string $repositoryName
     * @param string $gitLabProjectId
     * @param string $sourceCodeProvider
     * @param string $executionEnv
     * @param string $reportId
     */
    public function __construct(
        string $organizationName,
        string $repositoryName,
        string $gitLabProjectId,
        string $sourceCodeProvider,
        string $executionEnv,
        string $reportId
    ) {
        $this->organizationName = $organizationName;
        $this->repositoryName = $repositoryName;
        $this->gitLabProjectId = $gitLabProjectId;
        $this->sourceCodeProvider = $sourceCodeProvider;
        $this->executionEnv = $executionEnv;
        $this->reportId = $reportId;
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
    public function getGitLabProjectId(): string
    {
        return $this->gitLabProjectId;
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
    public function getExecutionEnv(): string
    {
        return $this->executionEnv;
    }

    /**
     * @return string
     */
    public function getReportId(): string
    {
        return $this->reportId;
    }
}
