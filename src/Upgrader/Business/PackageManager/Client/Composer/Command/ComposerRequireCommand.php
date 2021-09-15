<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Command;

use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollection;

class ComposerRequireCommand extends AbstractCommand
{
    protected const COMMAND_NAME = 'composer require';

    /**
     * @var \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection
     */
    protected $packageCollection;

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return sprintf('%s%s', static::COMMAND_NAME, $this->getPackageString());
    }

    /**
     * @return void
     */
    public function setPackageCollection(PackageCollection $packageCollection): void
    {
        $this->packageCollection = $packageCollection;
    }

    protected function getPackageString(): string
    {
        if (!$this->packageCollection) {
            throw new UpgraderException('ComposerUpdateCommand packageCollection property is not define');
        }

        $result = '';
        /** @var \Upgrader\Business\PackageManager\Entity\Package $package */
        foreach ($this->packageCollection->toArray() as $package) {
            $package = sprintf('%s:%s', $package->getName(), $package->getVersion());
            $result = sprintf('%s %s', $result, $package);
        }

        return $result;
    }
}
