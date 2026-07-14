<?php
/**
 * Shared entry point: the one call site the Elementor widget, the REST
 * controller, and the GraphQL resolver all use. Wraps the WordPress-
 * dependent repository fetch with the pure filter/sort module.
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Job_Listing_Service {

	public static function query( array $args ): array {
		$listings = Job_Listing_Repository::get_all();

		return Job_Listing_Filter::apply( $listings, $args, time() );
	}
}
