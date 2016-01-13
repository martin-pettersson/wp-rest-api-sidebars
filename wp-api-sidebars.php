<?php

/**
 * Short description...
 *
 * PHP version 5.4.0
 *
 * Copyright (C) 2015  Martin Pettersson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Martin Pettersson <martin_pettersson@outlook.com>
 * @copyright 2015 Martin Pettersson
 * @license   GPLv2
 * @link      https://github.com/martin-pettersson/wp-api-sidebars
 */

/**
 * Plugin Name: WP-API Sidebars
 * Plugin URI: https://github.com/martin-pettersson/wp-api-sidebars
 * Description: Exposes endpoints for widebars
 * Author: Martin Pettersson <martin_pettersson@outlook.com>
 * Author URI: http://martindotcom.se
 * Text Domain: wp-api-sidebars
 * Domain Path: /locale
 * Version: 0.1.0
 * Network: false
 * License: GPLv2
 */

// exit if accessed directly
defined( 'ABSPATH' ) || die;

defined( 'WP_VERSION' ) || define( 'WP_VERSION', get_bloginfo( 'version' ) );
define( 'WP_API_SIDEBARS_REQUIRED_PHP_VERSION', '5.4.0' );
define( 'WP_API_SIDEBARS_REQUIRED_WP_VERSION', '4.0' );
define( 'WP_API_SIDEBARS_ROOT_DIR', dirname( __FILE__ ) );
define( 'WP_API_SIDEBARS_ROOT_DIR_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'WP_API_SIDEBARS_ROOT_FILE', __FILE__ );
define( 'WP_API_SIDEBARS_PLUGIN_BASENAME', plugin_basename( WP_API_SIDEBARS_ROOT_FILE ) );
define( 'WP_API_SIDEBARS_VERSION', '0.1.0' );

// make sure we have a compatible environment
if (
    version_compare( PHP_VERSION, WP_API_SIDEBARS_REQUIRED_PHP_VERSION, '<' ) ||
    version_compare( WP_VERSION, WP_API_SIDEBARS_REQUIRED_WP_VERSION, '<' )
) {
    add_action( 'admin_notices', 'wp_api_sidebars_display_incompatible_environment_message' );
    add_action( 'admin_init', 'wp_api_sidebars_deactivate_plugin' );

    return;
}

// setup autoloader
if ( ! class_exists( 'WP_Autoloader' ) ) {
    require_once WP_API_SIDEBARS_ROOT_DIR . '/lib/wp-autoloader/class-wp-autoloader.php';
}

$loader = new WP_Autoloader;
$loader->add_namespace( 'WP_API_Sidebars', WP_API_SIDEBARS_ROOT_DIR . '/src' );
$loader->register();

$wp_api_sidebars = WP_API_Sidebars\Sidebars::get_instance( $loader );

// plug it in
add_action( 'plugins_loaded', [ $wp_api_sidebars, 'load' ] );
register_activation_hook( WP_API_SIDEBARS_ROOT_FILE, [ $wp_api_sidebars, 'activate' ] );
register_deactivation_hook( WP_API_SIDEBARS_ROOT_FILE, [ $wp_api_sidebars, 'deactivate' ] );
register_uninstall_hook( WP_API_SIDEBARS_ROOT_FILE, [ 'WP_API_Sidebars\Sidebars', 'uninstall' ] );

/**
 * Deactivates the plugin
 *
 * @return null
 */
function wp_api_sidebars_deactivate_plugin() {
    deactivate_plugins( WP_API_SIDEBARS_PLUGIN_BASENAME );
}

/**
 * Displays the "Incompatible environment" admin message
 *
 * @return null
 */
function wp_api_sidebars_display_incompatible_environment_message() {
    include WP_API_SIDEBARS_ROOT_DIR . '/templates/messages/incompatible-environment.php';
}
