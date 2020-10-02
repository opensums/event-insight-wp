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

$span = '<span style="font-family: Consolas, Monaco, monospace; background: rgba(0,0,0,0.07); padding: 0 4px;">';
$sc1 = '[tt-plus]';

?>

<div class="wrap">
<?php settings_errors() ?>
<h1><?php echo get_admin_page_title() ?></h1>

<?php /*
<h2>Getting started</h2>

<p>
Use the shortcode <?php echo($span.$sc1) ?></span> to do something.
</p>

<form action="options.php" method="post">
<?php
// output security fields for the registered setting "wporg"
settings_fields($pageSlug);
// output setting sections and their fields
do_settings_sections($pageSlug);
// output save settings button - moved up into sections.
// submit_button('Save Settings');
?>
<?php submit_button('Save settings'); ?>
</form>
*/
?>
<h3>Public Beta</h3>
<p>This is the first public beta version of Event Insight. Please bear with us
while we finish off a few tasks for the full release.</p>

<h3>Ticket Tailor Events</h3>

<p>
<button id="event-insight-get-ticket-tailor-events">Load events</button>
</p>

<div id="event-insight-ticket-tailor-events"></div>

<h3>Tickets</h3>
<div id="event-insight-ticket-tailor-tickets"></div>

<h2>Instructions</h2>

<ol>
    <li>
Click the "Load events" button to load events from Ticket Tailor.
    </li>
    <li>
Once the events are loaded, click on an event to load the tickets for that event.
    </li>
</ol>

<p>Set the secret API key to link your
<a href="https://www.tickettailor.com?fp_ref=opensums">TicketTailor</a>
account on the Secrets settings page.</p>

<p><small>This is <?php echo $plugin['name'] ?> version
<?php echo $plugin['version'] ?>.
</small></p>

</div>
