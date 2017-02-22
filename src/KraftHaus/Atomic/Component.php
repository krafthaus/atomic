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

use Closure;
use RuntimeException;
use Illuminate\View\View;
use KraftHaus\Atomic\Support\Facades\Atomic;

class Component
{
    /**
     * The array of children belonging to this Component.
     *
     * @var array
     */
    public $children = [];

    /**
     * The parent instance of this Component.
     *
     * @var Component
     */
    protected $parent;

    /**
     * Properties set on this Component.
     *
     * @var array
     */
    protected $props = [];

    /**
     * Holds the props that require validation
     * against the supplied entity data.
     *
     * @var array
     */
    protected $validate = [];

    /**
     * @param  array  $arguments
     */
    public function __construct(array $arguments = [])
    {
        // Set the properties for this component instance.
        if (isset($arguments['props'])) {
            $this->setProps($arguments['props']);
        }

        if (isset($arguments['children'])) {
            $children = $arguments['children'];

            // It's possible to pass a closure as a child element. When doing this,
            // you're limited to only one child element per component but gaining
            // the ability for more control over your child component.
            if ($children instanceof Closure) {
                $children($this);
            }

            // For each child, we'd like to pass the
            // properties given to that child object.
            if (is_array($children)) {
                foreach ($children as $child => $props) {
                    $this->addChild($child, $props);
                }
            }
        }
    }

    /**
     * @param  string  $method
     * @param  array  $arguments
     * @return Component
     */
    public function __call(string $method, array $arguments = []): Component
    {
        if (! app('atomic.registrar')->has($method)) {

            // When we're calling methods not set on this instance of it's children
            // we'd like to receive a proper error telling us that the
            // method we'd like to call is non-existing instead of
            // a warning that the return type of not correct.
            throw new RuntimeException(sprintf('Call to undefined method %s::%s()', __CLASS__, $method));
        }

        return $this->addChild($method, isset($arguments[0]) ? $arguments[0] : []);
    }

    /**
     * Get a prop by key.
     *
     * @param  string  $prop
     * @return mixed
     */
    public function __get(string $prop)
    {
        return $this->get($prop);
    }

    /**
     * Set a specific prop with value.
     *
     * @param  string  $prop
     * @param  mixed  $value
     */
    public function __set(string $prop, $value)
    {
        $this->set($prop, $value);
    }

    /**
     * Determine if a prop has been set.
     *
     * @param  string  $prop
     * @return bool
     */
    public function __isset(string $prop): bool
    {
        return $this->has($prop);
    }

    /**
     * Get a prop by key.
     *
     * @param  string  $prop
     * @return mixed
     */
    public function get(string $prop)
    {
        return $this->props[$prop];
    }

    /**
     * Set a specific prop with value.
     *
     * @param  string  $prop
     * @param  mixed  $value
     * @return Component
     */
    public function set(string $prop, $value): Component
    {
        $this->props[$prop] = $value;

        return $this;
    }

    /**
     * Determine if a prop has been set.
     *
     * @param  string  $prop
     * @return bool
     */
    public function has(string $prop): bool
    {
        return isset($this->props[$prop]);
    }

    /**
     * Get the mapped properties.
     *
     * @return array
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * Set an array of properties.
     *
     * @param  array  $props
     * @return Component
     */
    public function setProps(array $props): Component
    {
        foreach ($props as $prop => $value) {
            $this->set($prop, $value);
        }

        return $this;
    }

    /**
     * Get the required properties.
     *
     * @return array
     */
    public function getPropValidations(): array
    {
        return $this->validate;
    }

    /**
     * Add a new child to the tree.
     *
     * @param  string  $child
     * @param  array  $arguments
     * @return Component
     */
    public function addChild(string $child, array $arguments = []): Component
    {
        $namespace = app('atomic.registrar')->get($child);

        $instance = new $namespace($arguments);

        // Pass the current instance as a parent to the new instance
        // so that we later can reference the parent if we want.
        $instance->setParent($this);

        $this->children[] = $instance;

        return $instance;
    }

    /**
     * Render the component.
     *
     * @return string
     */
    public function render(): string
    {
        $view = $this->view();

        // When the components' view method outputs a View instance rather
        // then a string, we automatically push the current
        // component instance to this current view.
        if ($view instanceof View) {
            $view->with([
                'component' => $this,
                'props' => (object) $this->getProps(),
            ]);

            $view = $view->render();
        }

        return $view;
    }

    /**
     * @param  string  $entity
     * @param  string|null  $action
     * @return Component
     */
    public function include(string $entity, string $action = null): Component
    {
        $instance = Atomic::make($entity, $action);

        // Execute the entity and retrieve the constructed component instance
        // so that we can add it as a child object of this instance.
        $component = $instance->execute()->getComponent();

        $component->setParent($this);

        $this->children[] = $component;

        return $component;
    }

    /**
     * This view method is here to handle rendering of components
     * outside of the standard component set, e.g. includes...
     *
     * @return string
     */
    public function view()
    {
        $response = '';

        foreach ($this->children as $child) {
            $response .= $child->render();
        }

        return $response;
    }

    /**
     * Set the parent instance.
     *
     * @param  Component  $parent
     * @return Component
     */
    protected function setParent(Component $parent): Component
    {
        $this->parent = $parent;

        return $this;
    }
}
