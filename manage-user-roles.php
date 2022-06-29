<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.crestinfosystems.com/
 * @since             1.0.0
 * @package           Manage_User_Roles
 *
 * @wordpress-plugin
 * Plugin Name:       Manage User Roles
 * Plugin URI:        https://www.crestinfosystems.com/contact-us
 * Description:       To manage user role for all site at admin network.
 * Version:           1.0.0
 * Author:            Crest Infosystems Pvt. Ltd. 
 * Author URI:        https://www.crestinfosystems.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       manage-user-roles
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
define( 'MANAGE_USER_ROLES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-manage-user-roles-activator.php
 */
function activate_manage_user_roles() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-manage-user-roles-activator.php';
	Manage_User_Roles_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-manage-user-roles-deactivator.php
 */
function deactivate_manage_user_roles() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-manage-user-roles-deactivator.php';
	Manage_User_Roles_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_manage_user_roles' );
register_deactivation_hook( __FILE__, 'deactivate_manage_user_roles' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-manage-user-roles.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_manage_user_roles() {

	$plugin = new Manage_User_Roles();
	$plugin->run();

}
run_manage_user_roles();
