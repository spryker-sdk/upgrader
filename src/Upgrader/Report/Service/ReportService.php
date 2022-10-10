<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader\Report\Service;

use CodeCompliance\Domain\Entity\Report;
use CodeCompliance\Domain\Entity\Violation;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;
use Symfony\Component\Yaml\Yaml;
use Upgrader\Tasks\Evaluate\Analyze\AnalyzeTask;

class ReportService
{
    /**
     * @var string
     */
    public const FILE_PATH_PATTERN = '/reports/%s.violations.yaml';

    /**
     * @var string
     */
    protected const KEY_ATTRIBUTE_DOCUMENTATION = 'documentation';

    /**
     * @var string
     */
    protected const REPORTS_DIR = 'reports';

    /**
     * @var int
     */
    protected const LENGTH_MESSAGE_SEPARATOR = 100;

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
     * @param bool $isVerbose
     *
     * @return array<string>|null
     */
    public function getReport(): ?Report
    {
        $path = getcwd() . sprintf(static::FILE_PATH_PATTERN, AnalyzeTask::ID_ANALYZE_TASK);

        if (!file_exists($path)) {
            return null;
        }

        return Report::fromArray(Yaml::parseFile($path));
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     *
     * @return void
     */
    public function save(Report $report): void
    {
        $violationReportStructure = $report->toArray();

        if (!is_dir(static::REPORTS_DIR)) {
            mkdir(static::REPORTS_DIR, 0777, true);
        }

        $violationReportData = Yaml::dump($violationReportStructure);
        $reportPath = getcwd() . sprintf(static::FILE_PATH_PATTERN, AnalyzeTask::ID_ANALYZE_TASK);

        file_put_contents($reportPath, $violationReportData);
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Violation $violation
     * @param bool $isVerbose
     *
     * @return string
     */
    public function generateMessage(Violation $violation, bool $isVerbose = false): string
    {
        $key = $violation->producedBy();
        $message = $key . ' ' . $violation->getMessage();
        if ($violation->getSeverity() === ViolationInterface::SEVERITY_ERROR) {
            $message = sprintf('<error>%s</error>', $message);
        }
        $message .= PHP_EOL;

        if ($isVerbose) {
            $docUrl = $violation->getAdditionalAttributes()[static::KEY_ATTRIBUTE_DOCUMENTATION] ?? '';
            $message .= sprintf(
                '%s 💡More information: %s',
                $this->generateSeparator(strlen($key), ' '),
                $docUrl . PHP_EOL,
            );
        }

        $message .= $this->generateSeparator(strlen($key)) . ' ' . $this->generateSeparator(static::LENGTH_MESSAGE_SEPARATOR);

        return $message;
    }

    /**
     * @param int $length
     * @param string $char
     *
     * @return string
     */
    protected function generateSeparator(int $length, string $char = '-'): string
    {
        return str_pad('', $length, $char);
    }
}
