<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Response;

use Upgrader\Business\Upgrader\Response\UpgraderResponse;

class PackageManagerResponse extends UpgraderResponse
{
    /**
     * @var array
     */
    protected $packageList;

    /**
     * @param bool $isSuccessful
     * @param string|null $output
     * @param array $packageList
     */
    public function __construct(bool $isSuccessful, ?string $output = null, array $packageList = [])
    {
        parent::__construct($isSuccessful, $output);

        $this->packageList = $packageList;
    }

    /**
     * @return string|null
     */
    public function getOutput(): ?string
    {
        return 'PackageManager: ' . $this->output;
    }

    /**
     * @return array
     */
    public function getPackageList(): array
    {
        return $this->packageList;
    }

    /**
     * @return string|null
     */
    public function getRawOutput(): ?string
    {
        return $this->output;
    }
}
