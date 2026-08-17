<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Dto;

use DateTimeImmutable;

class ReportDto
{
    protected string $name;

    protected int $version;

    protected string $scope;

    protected DateTimeImmutable $createdAt;

    protected ReportPayloadDto $payload;

    protected ReportMetadataDto $metadata;

    public function __construct(
        string $name,
        int $version,
        string $scope,
        DateTimeImmutable $createdAt,
        ReportPayloadDto $payload,
        ReportMetadataDto $metadata
    ) {
        $this->name = $name;
        $this->version = $version;
        $this->scope = $scope;
        $this->createdAt = $createdAt;
        $this->payload = $payload;
        $this->metadata = $metadata;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPayload(): ReportPayloadDto
    {
        return $this->payload;
    }

    public function getMetadata(): ReportMetadataDto
    {
        return $this->metadata;
    }
}
