<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Serializer\Normalizer;

use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Upgrade\Application\Dto\ModelStatisticDto;
use Upgrade\Domain\Entity\Package;
use Upgrade\Infrastructure\Report\Dto\ReportDto;
use Upgrade\Infrastructure\Report\Dto\ReportMetadataDto;
use Upgrade\Infrastructure\Report\Dto\ReportPayloadDto;

class ReportNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $data
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ReportDto;
    }

    /**
     * @return array<class-string|'*'|'object'|string, bool|null>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [ReportDto::class => true];
    }

    /**
     * @param mixed $object
     * @param array<string, mixed> $context
     *
     * @throws \InvalidArgumentException
     *
     * @return array<string, mixed>
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        if (!($object instanceof ReportDto)) {
            throw new InvalidArgumentException(
                sprintf('Invalid incoming object %s only %s is supported', get_class($object), ReportDto::class),
            );
        }

        return [
            'name' => $object->getName(),
            'version' => $object->getVersion(),
            'scope' => $object->getScope(),
            'createdAt' => $object->getCreatedAt()->getTimestamp(),
            'payload' => $this->formatPayload($object->getPayload()),
            'metadata' => $this->formatMetaData($object->getMetadata()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatPayload(ReportPayloadDto $reportPayloadDto): array
    {
        return [
            'required_packages' => array_map([$this, 'formatPackage'], $reportPayloadDto->getRequiredPackages()),
            'dev_required_packages' => array_map([$this, 'formatPackage'], $reportPayloadDto->getDevRequiredPackages()),
            'integrator_warnings' => $reportPayloadDto->getIntegratorWarnings(),
            'module_statistic' => $reportPayloadDto->getModelStatisticDto() ? $this->formatModuleStatistic($reportPayloadDto->getModelStatisticDto()) : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatMetaData(ReportMetadataDto $metadataDto): array
    {
        return [
            'organization_name' => $metadataDto->getOrganizationName(),
            'repository_name' => $metadataDto->getRepositoryName(),
            'project_id' => $metadataDto->getProjectId(),
            'source_code_provider' => $metadataDto->getSourceCodeProvider(),
            'application_env' => $metadataDto->getAppEnv(),
            'report_id' => $metadataDto->getReportId(),
            'released' => $metadataDto->getReleased(),
            'id_rg' => $metadataDto->getIdRg(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatPackage(Package $package): array
    {
        return [
            'name' => $package->getName(),
            'version' => $package->getVersion(),
            'previous_version' => $package->getPreviousVersion(),
            'diff_link' => $package->getDiffLink(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatModuleStatistic(ModelStatisticDto $modelStatisticDto): array
    {
        return [
            'total_overwritten_models' => $modelStatisticDto->getTotalOverwrittenModels(),
            'total_changed_models' => $modelStatisticDto->getTotalChangedModels(),
            'total_intersecting_models' => $modelStatisticDto->getTotalIntersectingModels(),
            'intersecting_modules' => $modelStatisticDto->getIntersectingModules(),
        ];
    }
}
