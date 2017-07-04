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

use KraftHaus\Atomic\Exceptions\PropValidationException;

class Builder
{
    /**
     * Holds the current executed Entity instance.
     *
     * @var Entity
     */
    protected $entity;

    /**
     * @param  Entity  $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Build the entity.
     *
     * @param  Component  $component
     * @return Builder
     */
    public function build(Component $component): Builder
    {
        // Before we begin building the Entity with the mapped
        // data and properties we need to be sure everything
        // is all nice and tidy so let's try validation.
        $this->validate($component->getProps(), $component->getPropValidations());

        // Because we need the builder to run recursively on all
        // our mapped components we loop through all children
        // and call this build method on each of them.
        foreach ($component->children as $child) {
            $this->build($child);
        }

        return $this;
    }

    /**
     * @param  array  $data
     * @param  array  $props
     * @throws PropValidationException
     */
    protected function validate(array $data, array $props)
    {
        $validator = validator($data, $props);

        if ($validator->fails()) {
            throw new PropValidationException($validator);
        }
    }
}
