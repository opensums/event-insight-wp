<?php
/**
 * This file is part of the API Data plugin for WordPress™.
 *
 * @link      https://github.com/opensums/api-data-wp
 * @package   event-insight-wp
 * @copyright [OpenSums](https://opensums.com/)
 * @license   MIT
 */

declare(strict_types=1);

namespace EventInsight;

use EventInsight\WpPlugin\AdminPage;

/**
 */
class DataPage extends AdminPage {

    // Autoload these on admin pages.
    protected $assets = [
        [ 'style', 'wp-jquery-ui-dialog' ],
        [ 'style', 'tabulator-css', 'tabulator.min.css' ],
        [ 'script', 'tabulator', 'tabulator.min.js' ],
        [
            'script',
            'event-insight',
            'event-insight-wp.min.js',
            [
                'jquery',
                // Dialog box used for error notifications.
                'jquery-ui-dialog',
                // Make sure wpApiSettings is loaded.
                'wp-api-request',
            ],
        ],
    ];

    // protected $capability = 'event_insight_view_data';

    // Add a new menu section.
    protected $menuParent = null;

    protected $pageSlug = 'data';

    protected $pageTitle = 'Event Insight';

    protected $template = 'admin/data-page';
}
