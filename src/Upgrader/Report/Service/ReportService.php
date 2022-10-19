<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader\Report\Service;

use CodeCompliance\Domain\Entity\Report;
use CodeCompliance\Domain\Entity\ReportInterface;
use CodeCompliance\Domain\Entity\ViolationInterface;
use Symfony\Component\Yaml\Yaml;
use Upgrader\Tasks\Evaluate\Analyze\AnalyzeTask;

class ReportService implements ReportServiceInterface
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
     * @return \CodeCompliance\Domain\Entity\ReportInterface|null
     */
    public function getReport(): ?ReportInterface
    {
        $path = $this->getReportPath();

        if (!file_exists($path)) {
            return null;
        }

        return Report::fromArray(Yaml::parseFile($path));
    }

    /**
     * @param \CodeCompliance\Domain\Entity\ReportInterface $report
     *
     * @return void
     */
    public function saveReport(ReportInterface $report): void
    {
        $violationReportStructure = $report->toArray();

        if (!is_dir(static::REPORTS_DIR)) {
            mkdir(static::REPORTS_DIR, 0777, true);
        }

        $violationReportData = Yaml::dump($violationReportStructure);
        $reportPath = $this->getReportPath();

        file_put_contents($reportPath, $violationReportData);
    }

    /**
     * @param \CodeCompliance\Domain\Entity\ReportInterface $report
     * @param bool $isVerbose
     *
     * @return array<string>
     */
    public function generateMessages(ReportInterface $report, bool $isVerbose = false): array
    {
        $messages = [];
        foreach ($report->getViolations() as $violation) {
            $messages[] = $this->generateViolationMessage($violation, $isVerbose);
        }

        $messages[] = 'Total messages: ' . count($messages);

        return $messages;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\ViolationInterface $violation
     * @param bool $isVerbose
     *
     * @return string
     */
    protected function generateViolationMessage(ViolationInterface $violation, bool $isVerbose = false): string
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

    /**
     * @return string
     */
    protected function getReportPath(): string
    {
        return getcwd() . sprintf(static::FILE_PATH_PATTERN, AnalyzeTask::ID_ANALYZE_TASK);
    }
}
