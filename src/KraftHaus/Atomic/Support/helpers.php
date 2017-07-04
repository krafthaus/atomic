<?php

use KraftHaus\Atomic\Entity;
use KraftHaus\Atomic\Support\Facades\Atomic;

if (! function_exists('atomic')) {

    /**
     * Create a new Entity instance.
     *
     * @param  string  $entity
     * @param  string|null  $action
     * @return Entity
     */
    function atomic(string $entity, string $action = null): Entity
    {
        return Atomic::make($entity, $action);
    }
}