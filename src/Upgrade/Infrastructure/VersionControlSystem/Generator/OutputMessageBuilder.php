<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\Generator;

class OutputMessageBuilder
{
    /**
     * @return string
     */
    public function buildOutputMessage(string $prLink): string
    {
        $outputMessage = sprintf('Link to pull request %s', $prLink);
        $border = PHP_EOL . str_repeat('*', strlen($outputMessage)) . PHP_EOL;

        return sprintf('%s%s%s', $border, $outputMessage, $border);
    }
}
