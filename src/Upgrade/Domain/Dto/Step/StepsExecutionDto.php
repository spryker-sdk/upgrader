<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Dto\Step;

use PackageManager\Domain\Dto\ComposerLockDiffDto;

class StepsExecutionDto
{
    /**
     * @var bool
     */
    protected bool $isSuccessful;

    /**
     * @var array<string>
     */
    protected array $outputMessageList = [];

    /**
     * @var \PackageManager\Domain\Dto\ComposerLockDiffDto|null
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
        if ($outputMessage) {
            $this->outputMessageList[] = $outputMessage;
        }
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
        if (!$this->outputMessageList) {
            return null;
        }

        return implode(PHP_EOL, $this->outputMessageList);
    }

    /**
     * @param string|null $outputMessage
     *
     * @return $this
     */
    public function addOutputMessage(?string $outputMessage)
    {
        if ($outputMessage) {
            $this->outputMessageList[] = $outputMessage;
        }

        return $this;
    }

    /**
     * @param \PackageManager\Domain\Dto\ComposerLockDiffDto|null $composerLockDiffDto
     *
     * @return $this
     */
    public function addComposerLockDiff(?ComposerLockDiffDto $composerLockDiffDto)
    {
        $this->composerLockDiffDto = $composerLockDiffDto;

        return $this;
    }

    /**
     * @return \PackageManager\Domain\Dto\ComposerLockDiffDto|null
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
