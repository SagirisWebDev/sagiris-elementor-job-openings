<?php
/**
 * Plugin Name:       Sagiris Elementor Job Openings
 * Plugin URI:        https://github.com/SagirisWebDev/sagiris-elementor-job-openings
 * Description:       Custom Elementor widget for a company careers/job-openings list, backed by a shared query service exposed over REST and optional WPGraphQL.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * Author:            Sagiris Web Dev
 * Author URI:        https://sagirisdev.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sagiris-elementor-job-openings
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SAGIRIS_EJO_VERSION', '0.1.0' );
define( 'SAGIRIS_EJO_PATH', plugin_dir_path( __FILE__ ) );
define( 'SAGIRIS_EJO_URL', plugin_dir_url( __FILE__ ) );

require_once SAGIRIS_EJO_PATH . 'vendor/autoload.php';

add_action( 'plugins_loaded', array( Plugin::class, 'init' ) );

register_activation_hook( __FILE__, array( Plugin::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Plugin::class, 'deactivate' ) );
