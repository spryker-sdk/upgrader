<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Tasks\Resolve;

use Codebase\Infrastructure\Service\CodebaseService;
use SprykerSdk\SdkContracts\Entity\CommandInterface;
use SprykerSdk\SdkContracts\Entity\Lifecycle\LifecycleInterface;
use SprykerSdk\SdkContracts\Entity\PlaceholderInterface;
use SprykerSdk\SdkContracts\Entity\TaskInterface;
use Resolve\Application\Service\ResolveServiceInterface;
use Upgrader\Commands\Resolve\ResolveCommand;
use Upgrader\Configuration\ConfigurationProvider;
use Upgrader\Lifecycle\Lifecycle;

class ResolveTask implements TaskInterface
{

    /**
     * @var ResolveServiceInterface
     */
    protected ResolveServiceInterface $resolverService;

    /**
     * @var ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @param ResolveServiceInterface $resolverService
     * @param ConfigurationProvider $configurationProvider
     * @param CodebaseService $codebaseService
     */
    public function __construct(
        ResolveServiceInterface $resolverService,
        ConfigurationProvider $configurationProvider,
        CodebaseService $codebaseService
    ) {
        $this->resolverService = $resolverService;
        $this->configurationProvider = $configurationProvider;
        $this->codebaseService = $codebaseService;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return 'resolve:php:code';
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return 'Resolve your project non-unique properties for Spryker auto-upgrade';
    }

    /**
     * @return array<CommandInterface>
     */
    public function getCommands(): array
    {
        return [
            new ResolveCommand(
                $this->resolverService,
                $this->configurationProvider,
                $this->codebaseService,
            ),
        ];
    }

    /**
     * @return array<PlaceholderInterface>
     */
    public function getPlaceholders(): array
    {
        return [];
    }

    /**
     * @return string|null
     */
    public function getHelp(): ?string
    {
        return 'Helps you to auto-resolve all non-unique properties for upgrade.';
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return '0.1.0';
    }

    /**
     * @return bool
     */
    public function isDeprecated(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getSuccessor(): ?string
    {
        return null;
    }

    /**
     * @return LifecycleInterface
     */
    public function getLifecycle(): LifecycleInterface
    {
        return new Lifecycle();
    }

    /**
     * @return array<string>
     */
    public function getStages(): array
    {
        return [];
    }
}
