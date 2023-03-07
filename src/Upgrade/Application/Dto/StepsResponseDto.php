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
     * @var string
     */
    protected string $blockerInfo = '';

    /**
     * @var int|null
     */
    protected ?int $pullRequestId = null;

    /**
     * @var string|null
     */
    protected ?string $reportId = null;

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
     * @return array<string>
     */
    public function getOutputMessages(): array
    {
        return $this->outputMessageList;
    }

    /**
     * @param array<string> $outputMessageList
     *
     * @return $this
     */
    public function setOutputMessages(array $outputMessageList)
    {
        $this->outputMessageList = $outputMessageList;

        return $this;
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
     * @return $this
     */
    public function setIntegratorResponseDto(?IntegratorResponseDto $integratorResponseDto)
    {
        $this->integratorResponseDto = $integratorResponseDto;

        return $this;
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
     * @return $this
     */
    public function setPullRequestId(?int $pullRequestId)
    {
        $this->pullRequestId = $pullRequestId;

        return $this;
    }

    /**
     * @param string $blockerInfo
     *
     * @return $this
     */
    public function setBlockerInfo(string $blockerInfo)
    {
        $this->blockerInfo = $blockerInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockerInfo(): string
    {
        return $this->blockerInfo;
    }

    /**
     * @return string|null
     */
    public function getReportId(): ?string
    {
        return $this->reportId;
    }

    /**
     * @param string|null $reportId
     *
     * @return $this
     */
    public function setReportId(?string $reportId)
    {
        $this->reportId = $reportId;

        return $this;
    }
}
