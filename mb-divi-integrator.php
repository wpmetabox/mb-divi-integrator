<?php

/**
 * Plugin Name: Meta Box - Divi Integrator
 * Plugin URI:  https://metabox.io/plugins/mb-divi-integrator/
 * Description: Integrates Meta Box and Divi Page Builder.
 * Version:     0.1.0
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 * License:     GPL2+
 */

// Prevent loading this file directly.
defined( 'ABSPATH' ) || die;

require __DIR__ . '/vendor/autoload.php';

new MBDI\Main;
