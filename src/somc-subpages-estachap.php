<?php
/**
 * The Somc Subpages Estachap plugin as a WP shortcode or WP widget
 *
 * Based on WordPress Plugin Boilerplate plugin. A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Somc-subpages-estachap
 * @author    Estanislao Chapel <estanislao.chapel@stonebeach.se>
 * @license   GPL-2.0+
 * @link      http://plugins.stonebeach.se
 * @copyright 2014 Estanislao Chapel @ Stonebeach AB
 *
 * @wordpress-plugin
 * Plugin Name:       somc-subpages-estachap
 * Plugin URI:        http://plugins.stonebeach.se
 * Description:       An object-oriented Wordpress 3.5+ Plugin called 'somc-subpages-estachap' that can be used as a Wordpress Widget and as a Wordpress Shortcode (naming convention: [somc-subpages-estachap]). It displays all subpages of the page it is placed on.
 * Version:           1.0.0
 * Author:            Estanislao Chapel
 * Author URI:        http://plugins.stonebeach.se
 * Text Domain:       Somc-subpages-estachap
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/estachap/somc-subpages-estachap
 * Based on WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-somc-subpages-estachap.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/class-somc-subpages-estachap-widget.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'SomcSubpagesEstachap', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SomcSubpagesEstachap', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'SomcSubpagesEstachap', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'SomcSubpagesEstachapWidget', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * 
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

 	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-somc-subpages-estachap-admin.php' );
 	add_action( 'plugins_loaded', array( 'SomcSubpagesEstachap_Admin', 'get_instance' ) );

}
