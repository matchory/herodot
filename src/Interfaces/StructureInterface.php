<?php

declare(strict_types=1);

namespace Matchory\Herodot\Interfaces;

interface StructureInterface
{
    /**
     * Allows serializing structures in the configuration file
     *
     * @param array $array
     *
     * @return static
     */
    public static function __set_state(array $array): static;
}
