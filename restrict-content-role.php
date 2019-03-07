<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://simple-code.hr
 * @since             1.0.0
 * @package           Restrict_Content_Role
 *
 * @wordpress-plugin
 * Plugin Name:       Restrict Content By Role
 * Plugin URI:        https://simple-code.hr
 * Description:       Restrict content edit access by user role.
 * Version:           1.0.0
 * Author:            Simple Code d.o.o.
 * Author URI:        https://simple-code.hr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sc-restrict-content-role
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
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-restrict-content-role-activator.php
 */
function activate_restrict_content_role() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-restrict-content-role-activator.php';
	Restrict_Content_Role_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-restrict-content-role-deactivator.php
 */
function deactivate_restrict_content_role() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-restrict-content-role-deactivator.php';
	Restrict_Content_Role_Deactivator::deactivate();
}

/**
 * Include Redux plugin
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-redux-plugin.php';

/**
 * Register plugin activation & deactivation hooks
 */
register_activation_hook( __FILE__, 'activate_restrict_content_role' );
register_deactivation_hook( __FILE__, 'deactivate_restrict_content_role' );

/**
 * Register Redux activation & deactivation hooks
 */
register_activation_hook( __FILE__, array( 'ReduxFrameworkPlugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ReduxFrameworkPlugin', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-restrict-content-role.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_restrict_content_role() {

	ReduxFrameworkPlugin::instance();

	$plugin = new Restrict_Content_Role();
	$plugin->run();

}
run_restrict_content_role();
