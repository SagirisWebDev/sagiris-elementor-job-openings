<?php
/**
 * Plugin bootstrap: wires the job_listing post type and this plugin's
 * widget(s) into WordPress/Elementor.
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {

	public static function init(): void {
		add_action( 'init', array( Job_Listing_Post_Type::class, 'register' ) );
		add_action( 'add_meta_boxes', array( Job_Listing_Post_Type::class, 'register_meta_box' ) );
		add_action( 'save_post_job_listing', array( Job_Listing_Post_Type::class, 'save_meta' ) );
		add_filter( 'template_include', array( Job_Listing_Post_Type::class, 'template_include' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
		add_action( 'rest_api_init', array( REST_Controller::class, 'register_routes' ) );

		GraphQL_Type::maybe_register();
	}

	/**
	 * The job_listing CPT's custom rewrite slug needs a rewrite-rules flush
	 * to take effect - otherwise every listing permalink 404s until
	 * something else happens to trigger a flush (e.g. visiting Permalinks
	 * settings). register_post_type() only runs on `init`, which hasn't
	 * fired yet during activation, so it's registered here explicitly first.
	 */
	public static function activate(): void {
		Job_Listing_Post_Type::register();
		flush_rewrite_rules();
	}

	public static function deactivate(): void {
		flush_rewrite_rules();
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public static function register_widgets( $widgets_manager ): void {
		$widgets_manager->register( new Widget_Job_Openings() );
	}
}
