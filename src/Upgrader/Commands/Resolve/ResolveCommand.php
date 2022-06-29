<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Commands\Resolve;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Infrastructure\Service\CodebaseService;
use SprykerSdk\SdkContracts\Entity\ContextInterface;
use SprykerSdk\SdkContracts\Entity\ConverterInterface;
use SprykerSdk\SdkContracts\Entity\ExecutableCommandInterface;
use Resolve\Application\Service\ResolveServiceInterface;
use Resolve\Domain\Entity\Message;
use Upgrader\Configuration\ConfigurationProvider;

class ResolveCommand implements ExecutableCommandInterface
{
    /**
     * @var ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @var ResolveServiceInterface
     */
    protected ResolveServiceInterface $resolveService;

    /**
     * @param ResolveServiceInterface $resolveService
     * @param ConfigurationProvider $configurationProvider
     * @param CodebaseService $codebaseService
     */
    public function __construct(
        ResolveServiceInterface $resolveService,
        ConfigurationProvider $configurationProvider,
        CodebaseService $codebaseService
    ) {
        $this->resolveService = $resolveService;
        $this->configurationProvider = $configurationProvider;
        $this->codebaseService = $codebaseService;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'php';
    }

    /**
     * @return array<string>
     */
    public function getTags(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function hasStopOnError(): bool
    {
        return true;
    }

    /**
     * @return ConverterInterface|null
     */
    public function getViolationConverter(): ?ConverterInterface
    {
        return null;
    }

    /**
     * @param ContextInterface $context
     *
     * @return ContextInterface
     */
    public function execute(ContextInterface $context): ContextInterface
    {
        $codebaseRequestDto = new CodeBaseRequestDto(
            $this->configurationProvider->getToolingConfigurationFilePath(),
            $this->configurationProvider->getSrcPath(),
            $this->configurationProvider->getCorePaths(),
            $this->configurationProvider->getCoreNamespaces(),
            $this->configurationProvider->getIgnoreSources(),
        );

        $codebaseSourceDto = $this->codebaseService->readCodeBase($codebaseRequestDto);

        $response = $this->resolveService->resolve($codebaseSourceDto);

        $message = new Message($response->getMessage());

        $context->addMessage(static::class, $message);

        return $context;
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return ContextInterface::DEFAULT_STAGE;
    }
}
