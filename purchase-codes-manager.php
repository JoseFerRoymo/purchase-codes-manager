<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://roymo.es/
 * @since             1.0.0
 * @package           Purchase_Codes_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Purchase codes manager
 * Plugin URI:        https://https://roymo.es/
 * Description:       Manages users, generates a unique code for each of them and associates them to a purchase invoice.
 * Version:           1.0.0
 * Author:            Rommel & Montgomery
 * Author URI:        https://https://roymo.es//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       purchase-codes-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PURCHASE_CODES_MANAGER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-purchase-codes-manager-activator.php
 */
function activate_purchase_codes_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-purchase-codes-manager-activator.php';
	Purchase_Codes_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-purchase-codes-manager-deactivator.php
 */
function deactivate_purchase_codes_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-purchase-codes-manager-deactivator.php';
	Purchase_Codes_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_purchase_codes_manager' );
register_deactivation_hook( __FILE__, 'deactivate_purchase_codes_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-purchase-codes-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_purchase_codes_manager() {

	$plugin = new Purchase_Codes_Manager();
	$plugin->run();

}
run_purchase_codes_manager();
