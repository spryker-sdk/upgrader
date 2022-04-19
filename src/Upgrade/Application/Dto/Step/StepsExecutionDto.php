<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\Step;

use Upgrade\Application\Dto\Composer\ComposerLockDiffDto;

class StepsExecutionDto
{
    /**
     * @var bool
     */
    protected bool $isSuccessful;

    /**
     * @var string|null
     */
    protected ?string $outputMessage;

    /**
     * @var \Upgrade\Application\Dto\Composer\ComposerLockDiffDto|null
     */
    protected $composerLockDiffDto;

    /**
     * @var int|null
     */
    protected $pullRequestId;

    /**
     * @param bool $isSuccessful
     * @param string|null $outputMessage
     */
    public function __construct(bool $isSuccessful = true, ?string $outputMessage = null)
    {
        $this->isSuccessful = $isSuccessful;
        $this->outputMessage = $outputMessage;
    }

    /**
     * @return bool
     */
    public function getIsSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @param bool $isSuccessful
     *
     * @return $this
     */
    public function setIsSuccessful(bool $isSuccessful)
    {
        $this->isSuccessful = $isSuccessful;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOutputMessage(): ?string
    {
        return $this->outputMessage;
    }

    /**
     * @param string|null $outputMessage
     *
     * @return $this
     */
    public function setOutputMessage(?string $outputMessage = null)
    {
        $this->outputMessage = $outputMessage;

        return $this;
    }

    /**
     * @param \Upgrade\Application\Dto\Composer\ComposerLockDiffDto|null $composerLockDiffDto
     *
     * @return $this
     */
    public function addComposerLockDiff(?ComposerLockDiffDto $composerLockDiffDto)
    {
        $this->composerLockDiffDto = $composerLockDiffDto;

        return $this;
    }

    /**
     * @return \Upgrade\Application\Dto\Composer\ComposerLockDiffDto|null
     */
    public function getComposerLockDiff(): ?ComposerLockDiffDto
    {
        return $this->composerLockDiffDto;
    }

    /**
     * @return int|null
     */
    public function getPullRequestId(): ?int
    {
        return $this->pullRequestId;
    }

    /**
     * @param int|null $pullRequestId
     *
     * @return void
     */
    public function setPullRequestId(?int $pullRequestId): void
    {
        $this->pullRequestId = $pullRequestId;
    }
}
