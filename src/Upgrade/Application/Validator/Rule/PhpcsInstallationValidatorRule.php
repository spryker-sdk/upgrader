<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Validator\Rule;

use SprykerSdk\Utils\Infrastructure\Service\Filesystem;
use Upgrade\Application\Exception\UpgraderException;
use Upgrade\Application\Validator\ProjectValidatorRuleInterface;
use Upgrader\Configuration\ConfigurationProvider;

class PhpcsInstallationValidatorRule implements ProjectValidatorRuleInterface
{
    /**
     * @var string
     */
    public const VIOLATION_TITLE = 'Phpcs is not installed in the project';

    /**
     * @var string
     */
    public const PHP_CS_FIXER_PATH = 'vendor/bin/phpcbf';

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \SprykerSdk\Utils\Infrastructure\Service\Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \SprykerSdk\Utils\Infrastructure\Service\Filesystem $filesystem
     */
    public function __construct(ConfigurationProvider $configurationProvider, Filesystem $filesystem)
    {
        $this->configurationProvider = $configurationProvider;
        $this->filesystem = $filesystem;
    }

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(): void
    {
        $phpcsFilePath = $this->configurationProvider->getRootPath() . static::PHP_CS_FIXER_PATH;

        if (!$this->filesystem->exists($phpcsFilePath)) {
            throw new UpgraderException(
                sprintf('Unable to find phpcs fixer executable "%s". Please make sure that phpcs is installed.', static::PHP_CS_FIXER_PATH),
            );
        }
    }

    /**
     * @return string
     */
    public function getViolationTitle(): string
    {
        return static::VIOLATION_TITLE;
    }
}
