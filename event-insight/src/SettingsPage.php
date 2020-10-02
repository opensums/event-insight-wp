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
class SettingsPage extends AdminPage {

    protected $menuParent = 'event-insight-data';

    protected $menuLabel = 'Settings';

    protected $pageSlug = 'settings';

    protected $pageTitle = 'Event Insight Settings';

    // protected $sectionsTemplate = 'admin/settings-page-sections';
    protected $template = 'admin/settings-page';
}
