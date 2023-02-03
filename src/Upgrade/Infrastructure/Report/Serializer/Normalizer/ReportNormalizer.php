<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Serializer\Normalizer;

use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Upgrade\Domain\Entity\Package;
use Upgrade\Infrastructure\Report\Dto\ReportDto;
use Upgrade\Infrastructure\Report\Dto\ReportMetadataDto;
use Upgrade\Infrastructure\Report\Dto\ReportPayloadDto;

class ReportNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $data
     * @param string|null $format
     *
     * @return bool
     */
    public function supportsNormalization($data, ?string $format = null): bool
    {
        return $data instanceof ReportDto;
    }

    /**
     * @param mixed $object
     * @param string|null $format
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
     * @param \Upgrade\Infrastructure\Report\Dto\ReportPayloadDto $reportPayloadDto
     *
     * @return array<string, mixed>
     */
    protected function formatPayload(ReportPayloadDto $reportPayloadDto): array
    {
        return [
            'required_packages' => array_map([$this, 'formatPackage'], $reportPayloadDto->getRequiredPackages()),
            'dev_required_packages' => array_map([$this, 'formatPackage'], $reportPayloadDto->getDevRequiredPackages()),
            'integrator_warnings' => $reportPayloadDto->getIntegratorWarnings(),
        ];
    }

    /**
     * @param \Upgrade\Infrastructure\Report\Dto\ReportMetadataDto $metadataDto
     *
     * @return array<string, mixed>
     */
    protected function formatMetaData(ReportMetadataDto $metadataDto): array
    {
        return [
            'organization_name' => $metadataDto->getOrganizationName(),
            'repository_name' => $metadataDto->getRepositoryName(),
            'gitlab_project_id' => $metadataDto->getGitLabProjectId(),
            'source_code_provider' => $metadataDto->getSourceCodeProvider(),
            'execution_env' => $metadataDto->getExecutionEnv(),
            'report_id' => $metadataDto->getReportId(),
        ];
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
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
}
