<?php
/**
 * REST endpoint exposing the same job-listing data and filter/sort
 * behavior as the Elementor widget. Calls Job_Listing_Service - no
 * separate query logic lives here.
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class REST_Controller {

	const NAMESPACE_V1 = 'sagiris-job-openings/v1';

	public static function register_routes(): void {
		register_rest_route(
			self::NAMESPACE_V1,
			'/jobs',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_jobs' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'department' => array(
						'type' => 'string',
					),
					'location'   => array(
						'type' => 'string',
					),
					'sort'       => array(
						'type' => 'string',
						'enum' => array( 'newest', 'closing_soon' ),
					),
				),
			)
		);
	}

	public static function get_jobs( \WP_REST_Request $request ): \WP_REST_Response {
		$listings = Job_Listing_Service::query(
			array(
				'department' => (string) $request->get_param( 'department' ),
				'location'   => (string) $request->get_param( 'location' ),
				'sort'       => (string) $request->get_param( 'sort' ),
			)
		);

		return new \WP_REST_Response( array_map( array( __CLASS__, 'shape' ), $listings ) );
	}

	private static function shape( array $listing ): array {
		return array(
			'id'           => $listing['id'],
			'title'        => $listing['title'],
			'department'   => $listing['department'],
			'location'     => $listing['location'],
			'salary_range' => $listing['salary_range'],
			'closing_date' => $listing['closing_date'] ? gmdate( 'c', $listing['closing_date'] ) : null,
			'apply_url'    => $listing['apply_url'],
			'description'  => $listing['description'],
			'permalink'    => $listing['permalink'],
			'posted_date'  => gmdate( 'c', $listing['posted_at'] ),
		);
	}
}
