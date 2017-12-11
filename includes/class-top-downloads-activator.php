<?php

/**
 * Fired during plugin activation
 *
 * @link       https://accessidaho.org
 * @since      1.0.0
 *
 * @package    Top_Downloads
 * @subpackage Top_Downloads/includes
 */

class Top_Downloads_Activator {
	public static function activate() {
		global $wpdb;

		$table_name = $wpdb->prefix . "top_downloads";
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			attachment int NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
