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

/**
 * @codeCoverageIgnore
 *
 * Class latter will be moved into the commit comments
 *
 * @see https://spryker.atlassian.net/browse/SDK-2176
 */
class ViolationBodyMessageBuilder implements CheckerViolationMessageBuilderInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'PHP classes that became not compatible with Spryker Release';

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
        $header = static::ERROR_MESSAGE;

        $text = 'Switch to this branch, bootstrap your project in the development environment, open the mentioned file, and compare its correctness to the released version by Spryker.';
        $text .= PHP_EOL . PHP_EOL
            . '| Composer command | Project file(s) |'
            . PHP_EOL
            . '|------------------|-----------------|'
            . PHP_EOL;

        foreach ($violations as $violation) {
            $text .= '| ';

            $text .= implode(
                ' | ',
                [
                    $violation->getComposerCommands() ? implode('<br>', $violation->getComposerCommands()) : '-',
                    implode(
                        '<br>',
                        array_map(fn(FileErrorDto $fileErrorDto): string => '<b>' . $this->trimRootDir($fileErrorDto->getFilename()) . '</b><br>' . str_replace('|', '\|', $fileErrorDto->getMessage()) . '<br>', $violation->getFileErrors()),
                    ),
                ],
            );

            $text .= ' |' . PHP_EOL;
        }

        $text .= PHP_EOL;

        return "<details><summary><h4>$header</h4></summary>$text</details>";
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
