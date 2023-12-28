<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Domain\ValueObject\ErrorInterface;

/**
 * @codeCoverageIgnore
 */
class StepsResponseDto extends ResponseDto
{
    /**
     * @var int
     */
    public const UNDEFINED_RELEASE_GROUP_ID = 0;

    /**
     * @var bool
     */
    protected bool $isSuccessful;

    /**
     * @var array<string>
     */
    protected array $outputMessageList = [];

    /**
     * @var array<\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto>
     */
    protected array $appliedReleaseGroups = [];

    /**
     * @var \Upgrade\Application\Dto\ComposerLockDiffDto|null
     */
    protected ?ComposerLockDiffDto $composerLockDiffDto = null;

    /**
     * @var array<int, \Upgrade\Application\Dto\IntegratorResponseDto>
     */
    protected array $integratorResponseCollection = [];

    /**
     * @var \Upgrade\Domain\ValueObject\ErrorInterface|null
     */
    protected ?ErrorInterface $error = null;

    /**
     * @var array<int, array<\Upgrade\Application\Dto\ValidatorViolationDto>>
     */
    protected array $blockers = [];

    /**
     * @var array<\Upgrade\Application\Dto\ValidatorViolationDto>
     */
    protected array $projectViolations = [];

    /**
     * @var int|null
     */
    protected ?int $pullRequestId = null;

    /**
     * @var string|null
     */
    protected ?string $reportId = null;

    /**
     * @var bool
     */
    protected bool $isStopPropagation = false;

    /**
     * @var array<int, array<\Upgrade\Application\Dto\ViolationDtoInterface>>
     */
    protected array $violations = [];

    /**
     * @var array<\Upgrade\Application\Dto\ReleaseGroupFilterResponseDto>
     */
    protected array $filterResponseList = [];

    /**
     * @var \Upgrade\Application\Dto\ReleaseGroupStatDto
     */
    protected ReleaseGroupStatDto $releaseGroupStatDto;

    /**
     * @var \Upgrade\Application\Dto\ModelStatisticDto
     */
    protected ModelStatisticDto $modelStatisticDto;

    /**
     * @var \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto|null
     */
    protected ?ReleaseGroupDto $currentReleaseGroup = null;

    /**
     * @var bool
     */
    protected bool $isPullRequestSent = false;

    /**
     * @var string
     */
    protected string $targetBranch = '';

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

        $this->releaseGroupStatDto = new ReleaseGroupStatDto();
        $this->modelStatisticDto = new ModelStatisticDto();
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
     * @return \Upgrade\Domain\ValueObject\ErrorInterface|null
     */
    public function getError(): ?ErrorInterface
    {
        return $this->error;
    }

    /**
     * @param \Upgrade\Domain\ValueObject\ErrorInterface|null $error
     *
     * @return $this
     */
    public function setError(?ErrorInterface $error)
    {
        $this->error = $error;

        if ($error !== null) {
            $this->addOutputMessage($error->getErrorMessage());
        }

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
     * @return array<int, \Upgrade\Application\Dto\IntegratorResponseDto>
     */
    public function getIntegratorResponseCollection(): array
    {
        return $this->integratorResponseCollection;
    }

    /**
     * @param int $releaseGroupId
     *
     * @return \Upgrade\Application\Dto\IntegratorResponseDto|null
     */
    public function getIntegratorResponseDtoByReleaseGroupId(int $releaseGroupId): ?IntegratorResponseDto
    {
        return $this->integratorResponseCollection[$releaseGroupId] ?? null;
    }

    /**
     * @param \Upgrade\Application\Dto\IntegratorResponseDto $integratorResponseDto
     *
     * @return void
     */
    public function addIntegratorResponseDto(IntegratorResponseDto $integratorResponseDto): void
    {
        $this->integratorResponseCollection[$this->getCurrentReleaseGroupId()] = $integratorResponseDto;
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
     * @param \Upgrade\Application\Dto\ValidatorViolationDto $blockerInfo
     *
     * @return void
     */
    public function addBlocker(ValidatorViolationDto $blockerInfo): void
    {
        $currentReleaseGroupId = $this->getCurrentReleaseGroupId();

        if (!isset($this->blockers[$currentReleaseGroupId])) {
            $this->blockers[$currentReleaseGroupId] = [];
        }

        $this->blockers[$currentReleaseGroupId][] = $blockerInfo;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    public function removeBlockersByTitle(string $title): void
    {
        $currentReleaseGroupId = $this->getCurrentReleaseGroupId();

        if (!isset($this->blockers[$currentReleaseGroupId])) {
            return;
        }

        $this->blockers[$currentReleaseGroupId] = array_filter(
            $this->blockers[$currentReleaseGroupId],
            static fn (ValidatorViolationDto $violation): bool => $violation->getTitle() !== $title
        );
    }

    /**
     * @return array<int, array<\Upgrade\Application\Dto\ValidatorViolationDto>>
     */
    public function getBlockers(): array
    {
        return $this->blockers;
    }

    /**
     * @return bool
     */
    public function hasBlockers(): bool
    {
        return count($this->blockers) > 0;
    }

    /**
     * @param int $releaseGroupId
     *
     * @return array<\Upgrade\Application\Dto\ValidatorViolationDto>
     */
    public function getBlockersByReleaseGroupId(int $releaseGroupId): array
    {
        return $this->blockers[$releaseGroupId] ?? [];
    }

    /**
     * @return array<\Upgrade\Application\Dto\ValidatorViolationDto>
     */
    public function getProjectViolations(): array
    {
        return $this->projectViolations;
    }

    /**
     * @param \Upgrade\Application\Dto\ValidatorViolationDto $violationDto
     *
     * @return void
     */
    public function addProjectViolation(ValidatorViolationDto $violationDto): void
    {
        $this->projectViolations[] = $violationDto;
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

    /**
     * @return bool
     */
    public function getIsStopPropagation(): bool
    {
        return $this->isStopPropagation;
    }

    /**
     * @param bool $isStopPropagation
     *
     * @return $this
     */
    public function setIsStopPropagation(bool $isStopPropagation)
    {
        $this->isStopPropagation = $isStopPropagation;

        return $this;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto|null
     */
    public function getLastAppliedReleaseGroup(): ?ReleaseGroupDto
    {
        return end($this->appliedReleaseGroups) ?: null;
    }

    /**
     * @return array<int, \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto>
     */
    public function getAppliedReleaseGroups(): array
    {
        return $this->appliedReleaseGroups;
    }

    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto>
     */
    public function getAppliedSecurityFixedReleaseGroups(): array
    {
        return array_filter(
            $this->appliedReleaseGroups,
            static fn (ReleaseGroupDto $releaseGroup): bool => $releaseGroup->isSecurity()
        );
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $appliedReleaseGroup
     *
     * @return void
     */
    public function addAppliedReleaseGroup(ReleaseGroupDto $appliedReleaseGroup): void
    {
        $this->appliedReleaseGroups[$appliedReleaseGroup->getId()] = $appliedReleaseGroup;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto|null
     */
    public function getCurrentReleaseGroup(): ?ReleaseGroupDto
    {
        return $this->currentReleaseGroup;
    }

    /**
     * @return int
     */
    public function getCurrentReleaseGroupId(): int
    {
        return $this->currentReleaseGroup ? $this->currentReleaseGroup->getId() : static::UNDEFINED_RELEASE_GROUP_ID;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $currentReleaseGroup
     *
     * @return void
     */
    public function setCurrentReleaseGroup(ReleaseGroupDto $currentReleaseGroup): void
    {
        $this->currentReleaseGroup = $currentReleaseGroup;
    }

    /**
     * @return array<int, array<\Upgrade\Application\Dto\ViolationDtoInterface>>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * @param int $releaseGroupId
     *
     * @return array<\Upgrade\Application\Dto\ViolationDtoInterface>
     */
    public function getViolationsByReleaseGroupId(int $releaseGroupId): array
    {
        return $this->violations[$releaseGroupId] ?? [];
    }

    /**
     * @param \Upgrade\Application\Dto\ViolationDtoInterface $violation
     *
     * @return void
     */
    public function addViolation(ViolationDtoInterface $violation): void
    {
        $currentReleaseGroupId = $this->getCurrentReleaseGroupId();

        if (!isset($this->violations[$currentReleaseGroupId])) {
            $this->violations[$currentReleaseGroupId] = [];
        }

        $this->violations[$currentReleaseGroupId][] = $violation;
    }

    /**
     * @return \Upgrade\Application\Dto\ReleaseGroupStatDto
     */
    public function getReleaseGroupStatDto(): ReleaseGroupStatDto
    {
        return $this->releaseGroupStatDto;
    }

    /**
     * @param \Upgrade\Application\Dto\ReleaseGroupStatDto $releaseGroupStatDto
     *
     * @return void
     */
    public function setReleaseGroupStatDto(ReleaseGroupStatDto $releaseGroupStatDto): void
    {
        $this->releaseGroupStatDto = $releaseGroupStatDto;
    }

    /**
     * @return array<\Upgrade\Application\Dto\ReleaseGroupFilterResponseDto>
     */
    public function getFilterResponseList(): array
    {
        return $this->filterResponseList;
    }

    /**
     * @param \Upgrade\Application\Dto\ReleaseGroupFilterResponseDto $responseDto
     *
     * @return void
     */
    public function addFilterResponse(ReleaseGroupFilterResponseDto $responseDto): void
    {
        $this->filterResponseList[] = $responseDto;
    }

    /**
     * @return bool
     */
    public function isPullRequestSent(): bool
    {
        return $this->isPullRequestSent;
    }

    /**
     * @param bool $isPullRequestSent
     *
     * @return void
     */
    public function setIsPullRequestSent(bool $isPullRequestSent): void
    {
        $this->isPullRequestSent = $isPullRequestSent;
    }

    /**
     * @return string
     */
    public function getTargetBranch(): string
    {
        return $this->targetBranch;
    }

    /**
     * @param string $targetBranch
     *
     * @return void
     */
    public function setTargetBranch(string $targetBranch): void
    {
        $this->targetBranch = $targetBranch;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->getBlockers()) > 0
            || count($this->getViolations()) > 0
            || count(
                array_filter(
                    $this->getIntegratorResponseCollection(),
                    static fn (IntegratorResponseDto $response): bool => count($response->getWarnings()) > 0,
                ),
            ) > 0
            || count($this->getProjectViolations()) > 0;
    }

    /**
     * @return \Upgrade\Application\Dto\ModelStatisticDto
     */
    public function getModelStatisticDto(): ModelStatisticDto
    {
        return $this->modelStatisticDto;
    }

    /**
     * @param \Upgrade\Application\Dto\ModelStatisticDto $modelStatisticDto
     *
     * @return void
     */
    public function setModelStatisticDto(ModelStatisticDto $modelStatisticDto): void
    {
        $this->modelStatisticDto = $modelStatisticDto;
    }
}
