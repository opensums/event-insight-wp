<?php
/**
 * This file is part of the Event Insight plugin for WordPress™.
 *
 * @link      https://github.com/opensums/event-insight-wp
 * @package   event-insight-wp
 * @copyright [OpenSums](https://opensums.com/)
 * @license   MIT
 */

declare(strict_types=1);

namespace EventInsight;

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 */
class Plugin extends WpPlugin\Plugin {
    // --- YOU MUST OVERRIDE THESE IN THE PLUGIN CLASS -------------------------

    /** @var string Plugin human name. */
    protected $name = 'Event Insight';

    /** @var string Plugin slug (aka text domain). */
    protected $slug = 'event-insight';

    /** @var string Current version. */
    protected $version = '1.0.0';

    /** @var string[] Admin page class names. */
    protected $adminPages = [
        DataPage::class,
        SettingsPage::class,
        SecretsPage::class,
    ];

    /** @var string[] Option groups. */
    protected $optionsGroups = [
        'options' => Options::class,
        'secrets' => SecretOptions::class,
    ];
    // -------------------------------------------------------------------------

    public function restApi() {
        $this->container->get('routes')->register();
    }

    protected function childLoad() {
        add_action('rest_api_init', [$this, 'restApi']);
    }
};
