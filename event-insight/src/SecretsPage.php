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

use EventInsight\WpPlugin\AdminPage;

/**
 */
class SecretsPage extends AdminPage {

    protected $menuParent = 'event-insight-data';

    protected $menuLabel = 'Secrets';

    protected $pageSlug = 'secrets';

    protected $pageTitle = 'Event Insight Secrets';

    protected $sectionsTemplate = 'admin/settings-page-sections';
    protected $template = 'admin/secrets-page';

    protected function getSections() {

        return [
            // placeholder
            // helper to the right
            // supplemental underneath
            [
                // This is prefixed and used as the key in the wp_options table.
                'option' => 'secrets',
                // Used as the section element's id **without** prefixing.
                'id' => 'event-insight-secrets-ticket-tailor',
                'title' => __('Ticket Tailor settings', 'event-insight'),
                'fields' => [
                    [
                        // This is the option key.
                        'key' => 'ticket-tailor-api-key',
                        'label' => __('API Key', 'event-insight'),
                        'placeholder' => __('API Key', 'event-insight'),
                        'supplemental' => __('Create a key in the Ticket Tailor dashboard', 'event-insight'),
                        'size' => 48,
                    ],
                ],
            ],
        ];
    }
}
