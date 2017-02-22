<?php

namespace KraftHaus\Atomic\Exceptions;

/*
 * This file is part of the Atomic package.
 *
 * (c) KraftHaus <hello@krafthaus.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class UnknownComponentException extends \Exception
{
    /**
     * @param  string  $component
     */
    public function __construct(string $component)
    {
        parent::__construct('Unable to find component ' . $component);
    }
}
