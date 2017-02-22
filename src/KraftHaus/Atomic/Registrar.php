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

use Illuminate\Support\Collection;
use KraftHaus\Atomic\Exceptions\UnknownComponentException;

class Registrar
{
    /**
     * Holds the collection of registered components.
     *
     * @var Collection
     */
    protected $components;

    public function __construct()
    {
        $this->components = collect();
    }

    /**
     * Register a new component or an array of components.
     *
     * @param  mixed  $component
     * @param  string|null  $className
     * @return Registrar
     */
    public function register($component, string $className = null): Registrar
    {
        if (is_array($component)) {
            foreach ($component as $key => $value) {
                $this->register($key, $value);
            }
        } else {
            $this->components->put($component, $className);
        }

        return $this;
    }

    /**
     * Determine that a component exists.
     *
     * @param  string  $component
     * @return bool
     */
    public function has(string $component): bool
    {
        return $this->components->has($component);
    }

    /**
     * Return a specific component instance.
     *
     * @param  string  $component
     * @return string
     * @throws UnknownComponentException
     */
    public function get(string $component): string
    {
        if (! $this->has($component)) {
            throw new UnknownComponentException($component);
        }

        return $this->components->get($component);
    }
}
