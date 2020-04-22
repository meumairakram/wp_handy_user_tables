<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/meumairakram
 * @since             1.0.0
 * @package           Bea_user_tables
 *
 * @wordpress-plugin
 * Plugin Name:       Beautiful User Tables
 * Plugin URI:        https://github.com/meumairakram
 * Description:       A Custom Wordpress Plugin for Codeable Test Project to show beautiful User Tables
 * Version:           1.0.0
 * Author:            Umair Akram
 * Author URI:        https://github.com/meumairakram
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bea-user-tables
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
define( 'BEA_USER_TABLES_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bea_user_tables.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bea_user_tables() {

	$plugin = new Bea_user_tables();
	$plugin->run();

}
run_bea_user_tables();
