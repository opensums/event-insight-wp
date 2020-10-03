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

use EventInsight\WpPlugin\Plugin;

/**
 * Abstraction for the wp_options API.
 *
 * Access configuration with these methods:
 * * `$config->set('key', $anyValue)` to store $anyValue.
 * * `$config->get('key')` - to get a single value.
 * * `$config->all()` - to get all values.
 * * `$config->all(true)` - to get all loaded values.
 * * `$config->has('key')` - check if a key is defined.
 *
 * Configuration is set up using
 * * `$config->activate()
 * * `$config->uninstall()
 * 
 * Other
 * * `$config->flush() - to save all changes (also called by __destruct).
 */
class Options {

    /** @var string[] Dirty entries. */
    protected $dirty = [];

    /** @var string Parent plugin. */
    protected $plugin;

    /** @var mixed[] Values for entries that have been loaded. */
    protected $values = [];

    /**
     * Constructor.
     *
     * @param Plugin $plugin The plugin that owns the options.
     */
    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        // $this->wpOptionName = $plugin->slugify(str_to_lower($name), '_');
    }

    /**
     * Get the values of all entries, or all loaded entries.
     *
     * @param bool $loaded Iff true returns only loaded entries
     * @return mixed[] The entry values
     */
    public function all(string $group) {
        if (!array_key_exists($group, $this->values)) {
            if (!$this->loadGroup($group)) return null;
        }
        return $this->values[$group];
    }

    /**
     * 
     * Get the value of an entry.
     *
     * @param  string $group   The name of the settings group
     * @param  string $key     The key
     * @param  mixed  $default Default value
     * @return mixed  The value or the default value if the entry does not exist
     */
    public function get(string $group, string $key, $default = null) {
        if (!array_key_exists($group, $this->values)) {
            $this->loadGroup($group);
        }
        if (array_key_exists($key, $this->values[$group])) {
            return $this->values[$group][$key];
        }
        return $default;
    }

    /** Create a wp_options entry with optional autoload. */
    protected function addGroup(string $group, bool $autoload = false) {
        \add_option($this->plugin->slugify($group, '_'), [], null, $autoload);
    }

    protected function loadGroup($group) {
        \get_option($this->plugin->slugify($group, '_'));
    }

    // Refactor after here -----------------------------------------------------

    /**
     * Destructor.
     */
    public function __destruct() {
        $this->flush();
    }

    /**
     * Flush all persistent entries.
     *
     * @return self  Chainable
     */
    public function flush(): self {
        foreach ($this->dirty as $key => $isDirty) {
            if ($isDirty) {
                $this->wpOptionUpdate($key, $this->values[$key]);
            }
            $this->dirty = [];
        }
        return $this;
    }

    /**
     * Returns true iff the entry is defined.
     *
     * @param string $key The key
     *
     * @return bool true if the entry exists, false otherwise
     */
    public function has($key) {
        return array_key_exists($key, $this->values);
    }

    /**
     * Returns entry keys.
     *
     * @return array An array of parameter keys
     */
    public function keys(): array {
        return array_keys($this->values);
    }

    /**
     * Sets the value of an entry.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     * @return self  Chainable
     */
    public function set(string $key, $value): self {
        $this->values[$key] = $value;
        $this->dirty[$key] = true;
        return $this;
    }

    /**
     * Unsets (deletes) an entry.
     *
     * @param string $key   The key
     * @return self  Chainable
     */
    public function unset(string $key): self {
        if (array_key_exists($key, $this->values)) {
            unset($this->values[$key]);
        }
        $this->dirty[$key] = false;
        $this->wpOptionDelete($key);
        return $this;
    }

    /** Update a wp_options entry. */
    public function wpOptionUpdate(string $name, $value) {
        \update_option("{$this->wpPrefix}$name", $value);
    }

    /** Delete a wp_options entry. */
    public function wpOptionDelete(string $name) {
        \delete_option("{$this->wpPrefix}$name");
    }
}
