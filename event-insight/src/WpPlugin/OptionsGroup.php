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

use EventInsight\WpPlugin\Container;

class OptionsGroup {

    protected $keys = [];

    protected $wpOptionsName;

    final public function __construct(Container $container, string $name) {
        $this->container = $container;
        $this->wpOptionsName = $container->get('plugin')->slugify($name, '_');
    }

    public function all(): array {
        return $this->container->get('wp')->getOption($this->wpOptionsName, []);
    }

    public function registerSettingsForm($pageSlug): void {
        register_setting(
            $pageSlug,
            $this->wpOptionsName,
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitize'],
            ]
        );
    }

    /**
     * Sanitize user input for this options group.
     *
     * @param  mixed[] $dirty Assoc. array of dirty user input.
     * @return mixed[] Sanitized input.
     */
    public function sanitize($dirty): array {
        // If $dirty is null something has probably gone wrong.
        if (!is_array($dirty)) return [];
        $clean = $this->container->get('wp')->getOption($this->wpOptionsName, []);
        foreach ($dirty as $dirtyKey => $dirtyValue) {
            $sanitized = $this->sanitizeOne($dirtyKey, $dirtyValue);
            if ($sanitized) {
                $clean[$sanitized[0]] = $sanitized[1];
            }
        }
        return $clean;
    }

    public function isValidKey($dirtyKey) {
        return array_key_exists($dirtyKey, $this->keys);
    }

    public function sanitizeOne(string $dirtyKey, $dirtyValue): array {
        if (!$this->isValidKey($dirtyKey)) return [];
        return [$dirtyKey, substr($dirtyValue, 0, 128)];
    }
}
