<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Dto;

class PullRequestDto
{
    protected string $sourceBranch;

    protected string $targetBranch;

    protected string $title;

    protected ?string $body;

    protected bool $autoMerge;

    public function __construct(
        string $sourceBranch,
        string $targetBranch,
        string $title,
        ?string $body = null,
        bool $autoMerge = false
    ) {
        $this->sourceBranch = $sourceBranch;
        $this->targetBranch = $targetBranch;
        $this->title = $title;
        $this->body = $body;
        $this->autoMerge = $autoMerge;
    }

    public function getSourceBranch(): string
    {
        return $this->sourceBranch;
    }

    public function getTargetBranch(): string
    {
        return $this->targetBranch;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function isAutoMerge(): bool
    {
        return $this->autoMerge;
    }
}
