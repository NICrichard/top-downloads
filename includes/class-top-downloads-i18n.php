<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://accessidaho.org
 * @since      1.0.0
 *
 * @package    Top_Downloads
 * @subpackage Top_Downloads/includes
 */

class Top_Downloads_i18n {
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'top-downloads',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}
