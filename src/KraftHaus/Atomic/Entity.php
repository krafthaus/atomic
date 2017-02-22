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

class Entity
{
    /**
     * The current action that gets executed.
     *
     * @var string
     */
    protected $action;

    /**
     * The array of entity data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The component instance.
     *
     * @var null
     */
    protected $component = null;

    /**
     * The builder instance.
     *
     * @var null
     */
    protected $builder = null;

    /**
     * The default view.
     *
     * @var string
     */
    protected $view = 'atomic::entity';

    /**
     * Whether or not this entity has already been executed.
     *
     * @var bool
     */
    private $isExecuted = false;

    /**
     * @param  string  $action
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    /**
     * Render this entity. Beware, it's best to use the render
     * function directly when encountering exceptions because
     * the way PHP works, __toString methods cannot handle
     * exceptions on it's own.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Try to get a specific property set on the entity.
     *
     * @param  string  $prop
     * @return mixed
     */
    public function __get(string $prop)
    {
        // When the property is not available, we let PHP
        // handle the error itself because retrieving
        // a non-existent property should fail and
        // the programmer should handle this.
        return $this->data[$prop];
    }

    /**
     * Determine that a specific property has been set.
     *
     * @param  string  $prop
     * @return bool
     */
    public function __isset(string $prop): bool
    {
        return isset($this->data[$prop]);
    }

    /**
     * Add a piece of data to the view.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return Entity
     */
    public function with($key, $value = null): Entity
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get the mapped data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Render the entity.
     *
     * @param  string|null  $view
     * @return string
     */
    public function render(string $view = null): string
    {
        if (! $this->isExecuted) {

            // Apparently the entity isn't already executed
            // so let's do that before rendering.
            $this->execute();
        }

        // No specific view supplied, so fall back to the default.
        if (! $view) {
            $view = $this->view;
        }

        return view($view)->with([
            'entity' => $this,
            'component' => $this->getComponent(),
            'builder' => $this->getBuilder(),
        ])->render();
    }

    /**
     * Execute the entity.
     *
     * @return Entity
     */
    public function execute(): Entity
    {
        $action = $this->action;

        $component = new Component;

        $this->{$action}($component, $this->data);

        $builder = (new Builder($this))->build($component);

        // Store the component and builder instances on this
        // entity instance for possible later use.
        $this->component = $component;
        $this->builder = $builder;

        // Remember that we've executed the component and builder instances so that
        // we're able to safely can call the render method on this entity.
        $this->isExecuted = true;

        return $this;
    }

    /**
     * Get the component instance.
     *
     * @return Component
     */
    public function getComponent(): Component
    {
        return $this->component;
    }

    /**
     * Get the builder instance.
     *
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }
}
