<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://roymo.es/
 * @since      1.0.0
 *
 * @package    Purchase_Codes_Manager
 * @subpackage Purchase_Codes_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Purchase_Codes_Manager
 * @subpackage Purchase_Codes_Manager/includes
 * @author     Rommel & Montgomery <dev@roymo.es>
 */
class Purchase_Codes_Manager_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		create_purchases_table();
		create_user_role();
	}
}

function create_purchases_table(){
	global $wpdb;
	$table_name = $wpdb->prefix . "purchases";
	$charset_collate = $wpdb->get_charset_collate();
	$query_create = "CREATE TABLE IF NOT EXISTS $table_name (
		id INT NOT NULL AUTO_INCREMENT,
		name VARCHAR(200) NOT NULL,
		email VARCHAR(200) NOT NULL,
		phone VARCHAR(12) NOT NULL,
		location VARCHAR(600) NOT NULL,
		shop VARCHAR(600) NOT NULL,
		date_created DATETIME NOT NULL,
		date_redeemed DATETIME,
		draw_code VARCHAR(6),
		purchase_invoice VARCHAR(100),
		PRIMARY KEY (id)
	) $charset_collate";

	$wpdb->query($query_create);
}

function create_user_role(){
	add_role('coordinator', 'Coordinador', []);
}