<?php
/**
 * This file is part of the Event Insight plugin for WordPress™.
 *
 * @link      https://github.com/opensums/event-insight-wp
 * @package   event-insight-wp
 * @copyright [OpenSums](https://opensums.com/)
 * @license   MIT
 */

// Prevent direct access.
defined('ABSPATH') || exit;

?>

<div class="wrap">
<?php settings_errors() ?>
<h1><?php echo get_admin_page_title() ?></h1>

<form action="options.php" method="post">
<?php
// output security fields
settings_fields($pageSlug);
// output setting sections and their fields
do_settings_sections($pageSlug);
// output save settings button - moved up into sections.
// submit_button('Save Settings');
?>
<?php submit_button('Save settings'); ?>
</form>

</div>
