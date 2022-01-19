<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Evaluate\Infrastructure\Service;

use Evaluate\Infrastructure\Task\Analyze\AnalyzeTask;
use Symfony\Component\Yaml\Yaml;

class ReportService
{
    /**
     * @var string
     */
    protected const KEY_PRODUCED_BY = 'produced_by';

    /**
     * @var string
     */
    protected const KEY_MESSAGE = 'message';

    /**
     * @var string
     */
    protected const KEY_VIOLATIONS = 'violations';

    /**
     * @var \Symfony\Component\Yaml\Yaml;
     */
    protected Yaml $yaml;

    /**
     * @param \Symfony\Component\Yaml\Yaml $yaml
     */
    public function __construct(Yaml $yaml)
    {
        $this->yaml = $yaml;
    }

    /**
     * @return array|null
     */
    public function report(): ?array
    {
        $path = getcwd() . sprintf('/reports/%s.violations.yaml', AnalyzeTask::ID_ANALYZE_TASK);

        if (!file_exists($path)) {
            return null;
        }

        $output = Yaml::parseFile($path);

        $messages = [];
        $messageSeparator = str_pad('', 100, '-');
        foreach ($output[static::KEY_VIOLATIONS] as $violations) {
            $key = $violations[static::KEY_PRODUCED_BY];
            $keySeparatorLength = str_pad('', strlen($key), '-');
            $messages[] = $key . ' ' . $violations[static::KEY_MESSAGE] . PHP_EOL . $keySeparatorLength . ' ' . $messageSeparator;
        }

        return $messages;
    }
}
