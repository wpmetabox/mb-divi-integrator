<?php
/**
 * Plugin Name: MB Divi Integrator
 * Plugin URI:  https://metabox.io/plugins/mb-divi-integrator/
 * Description: Integrates Meta Box and Divi Page Builder.
 * Version:     1.0.0
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL2+
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || die;

if ( ! defined( 'MBDI_PATH' ) ) {
	if ( file_exists( __DIR__ . '/vendor' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	define( 'MBDI_PATH', plugin_dir_path( __FILE__ ) );

	new MBDI\Main;
}
