<?php

namespace KraftHaus\Atomic;

/*
 * This file is part of the Atomic package.
 *
 * (c) KraftHaus <hello@krafthaus.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Factory
{
    /**
     * @param  string  $entity
     * @param  string|null  $action
     * @return Entity
     */
    public function make(string $entity, string $action = null): Entity
    {
        if ($action === null) {
            if (! str_contains($entity, '@')) {
                throw new \InvalidArgumentException('No entity action provided.');
            }

            list($entity, $action) = explode('@', $entity);
        }

        return new $entity($action);
    }
}
