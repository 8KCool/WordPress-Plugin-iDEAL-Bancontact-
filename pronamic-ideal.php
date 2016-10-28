<?php
/*
Plugin Name: Pronamic iDEAL
Plugin URI: http://www.pronamic.eu/plugins/pronamic-ideal/
Description: The Pronamic iDEAL plugin allows you to easily add the iDEAL payment method to your WordPress website.

Version: 4.4.1
Requires at least: 4.3

Author: Pronamic
Author URI: http://www.pronamic.eu/

Text Domain: pronamic_ideal
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/pronamic/wp-pronamic-ideal
*/

/**
 * Autoload
 */
if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
} elseif ( version_compare( PHP_VERSION, '5.2', '>=' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload_52.php';
}

/**
 * Bootstrap
 */
global $pronamic_ideal;

$pronamic_ideal = Pronamic_WP_Pay_Plugin::bootstrap( __FILE__ );
