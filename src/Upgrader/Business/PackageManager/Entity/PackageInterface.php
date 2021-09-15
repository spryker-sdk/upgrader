<?php

namespace Upgrader\Business\PackageManager\Entity;

interface PackageInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getVersion(): string;
}
