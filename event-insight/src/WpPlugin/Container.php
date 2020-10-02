<?php
/**
 * This file is part of the Event Insight plugin for WordPress™.
 *
 * @link      https://github.com/opensums/event-insight-wp
 * @package   event-insight-wp/wp-plugin
 * @copyright [OpenSums](https://opensums.com/)
 * @license   MIT
 */

declare(strict_types=1);

namespace EventInsight\WpPlugin;

class Container {

    /** @var array Entry definitions. */
    protected $definitions = [];

    /** @var array Entries. */
    protected $entries = [];

    /**
     * Define an entry or entries for lazy-loading.
     *
     * @param  string  $idOrArray   Identifier of the entry to define.
     * @param  array   $idOrArray   Associative array of definitions keyed by identifier.
     * @param  mixed   $definition  The definition to be set.
     * @return $this   Chainable.
    **/
    public function define($id, $definition = null) : self {
        if (is_array($id)) {
            $this->definitions = array_merge($this->definitions, $id);
            return $this;
        }
        $this->definitions[$id] = $definition;
        return $this;
    }

    /**
     * PSR-11 compatible method to retrieve an entry, lazy-loading it if required.
     *
     * @param string $id Identifier of the entry to get.
     *
     * @throws ContainerNotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed The entry.
    **/
    public function get($id) {
        // Load the entry if not already loaded.
        if (!array_key_exists($id, $this->entries)) {
            $this->load($id);
        }
        return $this->entries[$id];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to get.
     * @return bool
    **/
    public function has($id) {
        return array_key_exists($id, $this->definitions)
            || array_key_exists($id, $this->entries);
    }

    /**
     * Load a defined entry.
     *
     * @param  string  $id    Identifier of the definition to load.
     * @param  string  $asId  Optional identifier to load the definition as.
     * @param  bool    $asId  Set to false to return the entry without loading it.
     * @return mixed   The value of the entry.
    **/
    public function load(string $id, $asId = null) {
        if ($asId === null) {
            $asId = $id;
        } elseif ($asId === false) {
            $asId = null;
        }

        // Find the defintion.
        if (!array_key_exists($id, $this->definitions)) {
            throw new \Exception(strtr(
                __('Entry ":id" has not been defined in this container', 'event-insight'), [
                    ':id' => $id,
                ]
            ));
        }

        $definition = $this->definitions[$id];

        // Check for an illegal entry id.
        if ($asId === '_definitions') {
            throw new \Exception(strtr(
                __('Cannot create a container entry with the id ":id"', 'event-insight'), [
                    ':id' => $id,
                ]
            ));
        }

        try {
            $entry = $this->loadDefinition($definition, $asId);
        } catch (\Throwable $error) {
            throw new \Exception(strtr(
                __('Cannot create a container entry with the id ":id"', 'event-insight'), [
                    ':id' => $id,
                ]
            ));
        }

        if ($asId === null) return $entry;

        $this->entries[$asId] = $entry;
    }

    /**
     * Load a defined entry.
     *
     * @param  mixed   $definition    The definition to load.
     * @param  string  $asId          Optional identifier to load the definition as.
     * @return mixed   The value of the entry.
    **/
    protected function loadDefinition($definition, string $asId = null) {

        // Load a service from a Closure.
        if (is_object($definition) && gettype($definition) === \Closure::class) {
            return $definition->call($this, $asId);
        }

        // Load a service from a factory method (now we know it is not a Closure).
        if (is_callable($definition)) {
            return $definition($this, $asId);

        }

        // Load a singleton service.
        if (class_exists($definition)) {
            return new $definition($this, $asId);
        }

        // Load anything else.
        return $definition;
    }
}
