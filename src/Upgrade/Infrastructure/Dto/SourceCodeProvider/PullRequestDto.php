<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Dto\SourceCodeProvider;

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
    protected bool $autoMerge = false;

    /**
     * @param string $sourceBranch
     * @param string $targetBranch
     * @param string $title
     */
    public function __construct(string $sourceBranch, string $targetBranch, string $title)
    {
        $this->sourceBranch = $sourceBranch;
        $this->targetBranch = $targetBranch;
        $this->title = $title;
    }

    /**
     * @param string|null $body
     *
     * @return void
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    /**
     * @param bool $autoMerge
     *
     * @return void
     */
    public function setAutoMerge(bool $autoMerge): void
    {
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
