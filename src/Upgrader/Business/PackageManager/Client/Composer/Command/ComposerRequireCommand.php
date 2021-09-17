<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Command;

use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface;

class ComposerRequireCommand extends AbstractCommand implements ComposerRequireCommandInterface
{
    protected const COMMAND_NAME = 'composer require';

    /**
     * @var \Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface|null
     */
    protected $packageCollection;

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Composer require';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'The command add composer packages.';
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return sprintf('%s%s', static::COMMAND_NAME, $this->getPackageString());
    }

    /**
     * @param \Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface $packageCollection
     *
     * @return bool
     */
    public function setPackageCollection(PackageCollectionInterface $packageCollection): bool
    {
        $this->packageCollection = $packageCollection;

        return true;
    }

    /**
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return string
     */
    protected function getPackageString(): string
    {
        if (!$this->packageCollection) {
            throw new UpgraderException('ComposerUpdateCommand packageCollection property is not define');
        }

        $result = '';
        foreach ($this->packageCollection->toArray() as $package) {
            $package = sprintf('%s:%s', $package->getName(), $package->getVersion());
            $result = sprintf('%s %s', $result, $package);
        }

        return $result;
    }
}
