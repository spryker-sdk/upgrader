<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Presentation\Checker\BrokenPhpFilesChecker;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto;
use Upgrade\Infrastructure\VersionControlSystem\Generator\CheckerViolationMessageBuilderInterface;
use Upgrader\Configuration\ConfigurationProvider;

class ViolationBodyMessageBuilder implements CheckerViolationMessageBuilderInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'Issues in php files after "composer require" command';

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto> $violations
     * @param array<\Upgrade\Domain\Entity\Package> $packageDtos
     *
     * @return string
     */
    public function buildViolationsMessage(array $violations, array $packageDtos): string
    {
        $header = ':warning: ' . static::ERROR_MESSAGE;

        $text = '<br>Switch to this branch, bootstrap your project in the development environment, open the mentioned file and check it\'s correctness compared to the released one by Spryker';
        $text .= PHP_EOL . PHP_EOL
            . '| Composer command | Project file(s) | '
            . PHP_EOL
            . '|------------------|-----------------|'
            . PHP_EOL;

        foreach ($violations as $violation) {
            $text .= implode(
                ' | ',
                [
                    implode('<br>', $violation->getComposerCommands()),
                    implode(
                        '<br>',
                        array_map(fn (FileErrorDto $fileErrorDto): string => '<b>' . $this->trimRootDir($fileErrorDto->getFilename()) . '</b><br>' . str_replace('|', '\|', $fileErrorDto->getMessage()) . '<br>', $violation->getFileErrors()),
                    ),
                    PHP_EOL,
                ],
            );
        }

        $text .= PHP_EOL;

        return "<details><summary>$header</summary>$text</details>";
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    protected function trimRootDir(string $fileName): string
    {
        $rootDir = $this->configurationProvider->getRootPath();

        if (strpos($fileName, $rootDir) !== 0) {
            return $fileName;
        }

        return mb_substr($fileName, mb_strlen($rootDir));
    }

    /**
     * @return string
     */
    public function getSupportedType(): string
    {
        return ViolationDto::class;
    }
}
