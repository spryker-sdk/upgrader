<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\VersionControlSystem\Dto;

class PullRequestDto
{
    /**
     * @var string
     */
    protected string $sourceBranch;

    /**
     * @var string
     */
    protected string $targetBranch;

    /**
     * @var string
     */
    protected string $title;

    /**
     * @var ?string
     */
    protected ?string $body;

    /**
     * @var bool
     */
    protected bool $autoMerge;

    /**
     * @param string $sourceBranch
     * @param string $targetBranch
     * @param string $title
     * @param string|null $body
     * @param bool $autoMerge
     */
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

    /**
     * @return string
     */
    public function getSourceBranch(): string
    {
        return $this->sourceBranch;
    }

    /**
     * @return string
     */
    public function getTargetBranch(): string
    {
        return $this->targetBranch;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @return bool
     */
    public function isAutoMerge(): bool
    {
        return $this->autoMerge;
    }
}
