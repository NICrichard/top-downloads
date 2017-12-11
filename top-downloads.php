<?php

/**
 * Plugin Name:       Top Downloads
 * Plugin URI:        https://accessidaho.org
 * Description:       Does just as the title suggests.
 * Version:           1.0.0
 * Author:            Access Idaho
 * Author URI:        https://accessidaho.org
 * License:           MIT
 * Text Domain:       top-downloads
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-top-downloads-activator.php
 */
function activate_top_downloads() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-top-downloads-activator.php';
	Top_Downloads_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-top-downloads-deactivator.php
 */
function deactivate_top_downloads() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-top-downloads-deactivator.php';
	Top_Downloads_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_top_downloads');
register_deactivation_hook(__FILE__, 'deactivate_top_downloads');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-top-downloads.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_top_downloads() {
	$plugin = new Top_Downloads();
	$plugin->run();
}
run_top_downloads();
