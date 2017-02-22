<?php

namespace KraftHaus\Atomic\Support\Facades;

/*
 * This file is part of the Atomic package.
 *
 * (c) KraftHaus <hello@krafthaus.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\Facades\Facade;

class Atomic extends Facade
{

    /**
     * Get the registered name of the entity.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'atomic.factory';
    }
}