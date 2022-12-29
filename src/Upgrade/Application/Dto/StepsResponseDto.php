<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class StepsResponseDto extends ResponseDto
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
     * @var \Upgrade\Application\Dto\ComposerLockDiffDto|null
     */
    protected ?ComposerLockDiffDto $composerLockDiffDto = null;

    /**
     * @var \Upgrade\Application\Dto\IntegratorResponseDto|null
     */
    protected ?IntegratorResponseDto $integratorResponseDto = null;

    /**
     * @var string|null
     */
    protected ?string $majorAvailableInfo = null;

    /**
     * @var int|null
     */
    protected ?int $pullRequestId = null;

    /**
     * @param bool $isSuccessful
     * @param string|null $outputMessage
     */
    public function __construct(bool $isSuccessful = true, ?string $outputMessage = null)
    {
        parent::__construct($isSuccessful);
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
     * @param \Upgrade\Application\Dto\ComposerLockDiffDto|null $composerLockDiffDto
     *
     * @return $this
     */
    public function setComposerLockDiff(?ComposerLockDiffDto $composerLockDiffDto)
    {
        $this->composerLockDiffDto = $composerLockDiffDto;

        return $this;
    }

    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto|null
     */
    public function getComposerLockDiff(): ?ComposerLockDiffDto
    {
        return $this->composerLockDiffDto;
    }

    /**
     * @return \Upgrade\Application\Dto\IntegratorResponseDto|null
     */
    public function getIntegratorResponseDto(): ?IntegratorResponseDto
    {
        return $this->integratorResponseDto;
    }

    /**
     * @param \Upgrade\Application\Dto\IntegratorResponseDto|null $integratorResponseDto
     *
     * @return void
     */
    public function setIntegratorResponseDto(?IntegratorResponseDto $integratorResponseDto): void
    {
        $this->integratorResponseDto = $integratorResponseDto;
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

    /**
     * @param string|null $majorAvailableWarning
     *
     * @return void
     */
    public function setMajorAvailableInfo(?string $majorAvailableWarning): void
    {
        $this->majorAvailableInfo = $majorAvailableWarning;
    }

    /**
     * @return string|null
     */
    public function getMajorAvailableInfo(): ?string
    {
        return $this->majorAvailableInfo;
    }
}
