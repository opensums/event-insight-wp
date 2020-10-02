<?php
/**
 * This file is part of the Event Insight plugin for WordPress™.
 *
 * @link      https://github.com/opensums/event-insight-wp
 * @package   event-insight-wp
 * @copyright [OpenSums](https://opensums.com/)
 * @license   MIT
 *
 * @wordpress-plugin
 * Plugin Name:       Event Insight
 * Plugin URI:        http://github.com/opensums/event-insight-wp/
 * Description:       Integrate data from various APIs.
 * Version:           1.0.0
 * Author:            OpenSums
 * Author URI:        https://opensums.com/
 * License:           MIT
 * License URI:       https://github.com/opensums/event-insight-wp/LICENSE
 * Text Domain:       event-insight
 * Domain Path:       /languages
 */

namespace EventInsight;

// If this file is called directly, abort.
if (!defined('WPINC')) die;

// Register a really simple class autoloader.
spl_autoload_register(function ($class) {
    $len = strlen(__NAMESPACE__);
    $path = __DIR__.'/src';
    if (strncmp(__NAMESPACE__, $class, $len) === 0) {
        $file = __DIR__.'/src'.str_replace('\\', '/', substr($class, $len)).'.php';
        if (file_exists($file)) require $file;
    }
});

(new WpPlugin\Container())
    ->define([
        'basedir' => __DIR__,
        'wp' => WpPlugin\Wp::class,
        'plugin' => Plugin::class,
        'secrets' => SecretOptions::class,
        'options' => Options::class,
        'ticket-tailor' => TicketTailor\Api::class,
        'routes' => Routes::class,
    ])
    ->get('plugin')
    ->load();

return;

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_plugin_name() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name-activator.php';
    Plugin_Name_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_plugin_name() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name-deactivator.php';
    Plugin_Name_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name.php';
