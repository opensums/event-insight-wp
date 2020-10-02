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

$tt = '<span style="font-family: Consolas, Monaco, monospace; background: rgba(0,0,0,0.07); padding: 0 4px;">';
?>

<div class="wrap">
<?php settings_errors() ?>
<h1><?php echo get_admin_page_title() ?></h1>


<form action="options.php" method="post">
<?php
// Output the fields and sections.
settings_fields($pageSlug);
do_settings_sections($pageSlug);
?>

<?php submit_button('Save settings'); ?>
</form>

<h2>About this plugin</h2>

<p>
This is <?php echo $plugin['name'] ?> version
<?php echo $plugin['version'] ?>.
</p>

</div>
