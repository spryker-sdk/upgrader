<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\VersionControlSystem\SourceCodeProvider\Azure;

class AzurePullRequestDescriptionNormalizer
{
    /**
     * @var int
     */
    protected const MAX_PR_DESCRIPTION_LENGTH = 4000;

    protected int $maxDescriptionLength;

    public function __construct(int $maxDescriptionLength = self::MAX_PR_DESCRIPTION_LENGTH)
    {
        $this->maxDescriptionLength = $maxDescriptionLength;
    }

    public function normalize(string $prDescription, ?string $reportId): string
    {
        if (mb_strlen($prDescription) <= $this->maxDescriptionLength) {
            return $prDescription;
        }

        $description = $this->cutDescriptionToText($prDescription, '**Packages upgraded:**');

        return $description ?? $this->getDefaultDescription($reportId);
    }

    protected function cutDescriptionToText(string $description, string $text): ?string
    {
        $cutLine = mb_strpos($description, $text);

        if ($cutLine === false) {
            return null;
        }

        $description = mb_substr($description, 0, $cutLine);

        if (mb_strlen($description) <= $this->maxDescriptionLength) {
            return $description;
        }

        return null;
    }

    protected function getDefaultDescription(?string $reportId): string
    {
        return 'Auto created via Upgrader tool.'
            . PHP_EOL
            . PHP_EOL
            . '#### Overview'
            . PHP_EOL
            . sprintf('Report ID: %s', $reportId ?? 'n/a')
            . PHP_EOL
            . PHP_EOL;
    }
}
