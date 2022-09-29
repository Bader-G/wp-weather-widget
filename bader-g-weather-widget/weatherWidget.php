<?php
/**
 * @package WeatherWidget
 */
/*
Plugin Name: Bader's Dashboard Weather Widget
Plugin URI: https://bader-g.github.io
Description: A weather widget that is shown on the dashboard
Version: 1.0
Author: Bader
Author URI: https://bader-g.github.io
License: GPLv2 or later
Text Domain: WeatherWidget
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
define( 'WeatherWidget__PLUGIN_DIR', plugin_dir_path(__FILE__) );

require_once( WeatherWidget__PLUGIN_DIR . 'admin/class.badersWeather.php' );
require_once( WeatherWidget__PLUGIN_DIR . 'admin/setup.php' );