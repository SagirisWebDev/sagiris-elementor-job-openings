<?php
/**
 * Optional GraphQL support: registers a `jobListings` root field and
 * `JobListing` object type, but only if WPGraphQL is active. The resolver
 * calls Job_Listing_Service - same shared entry point as every other
 * consumer.
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GraphQL_Type {

	public static function maybe_register(): void {
		if ( ! class_exists( '\WPGraphQL' ) ) {
			return;
		}

		add_action( 'graphql_register_types', array( __CLASS__, 'register_types' ) );
	}

	public static function register_types(): void {
		register_graphql_enum_type(
			'JobListingSortEnum',
			array(
				'description' => __( 'Sort order for job listings.', 'sagiris-elementor-job-openings' ),
				'values'      => array(
					'NEWEST'       => array( 'value' => 'newest' ),
					'CLOSING_SOON' => array( 'value' => 'closing_soon' ),
				),
			)
		);

		register_graphql_object_type(
			'JobListing',
			array(
				'description' => __( 'A job opening.', 'sagiris-elementor-job-openings' ),
				'fields'      => array(
					'id'          => array( 'type' => 'Int' ),
					'title'       => array( 'type' => 'String' ),
					'department'  => array( 'type' => 'String' ),
					'location'    => array( 'type' => 'String' ),
					'salaryRange' => array( 'type' => 'String' ),
					'closingDate' => array( 'type' => 'String' ),
					'applyUrl'    => array( 'type' => 'String' ),
					'description' => array( 'type' => 'String' ),
					'permalink'   => array( 'type' => 'String' ),
					'postedDate'  => array( 'type' => 'String' ),
				),
			)
		);

		register_graphql_field(
			'RootQuery',
			'jobListings',
			array(
				'type'        => array( 'list_of' => 'JobListing' ),
				'description' => __( 'Current job listings.', 'sagiris-elementor-job-openings' ),
				'args'        => array(
					'department' => array( 'type' => 'String' ),
					'location'   => array( 'type' => 'String' ),
					'sort'       => array( 'type' => 'JobListingSortEnum' ),
				),
				'resolve'     => static function ( $root, array $args ) {
					$listings = Job_Listing_Service::query(
						array(
							'department' => $args['department'] ?? '',
							'location'   => $args['location'] ?? '',
							'sort'       => $args['sort'] ?? 'newest',
						)
					);

					return array_map( array( __CLASS__, 'shape' ), $listings );
				},
			)
		);
	}

	private static function shape( array $listing ): array {
		return array(
			'id'          => $listing['id'],
			'title'       => $listing['title'],
			'department'  => $listing['department'],
			'location'    => $listing['location'],
			'salaryRange' => $listing['salary_range'],
			'closingDate' => $listing['closing_date'] ? gmdate( 'c', $listing['closing_date'] ) : null,
			'applyUrl'    => $listing['apply_url'],
			'description' => $listing['description'],
			'permalink'   => $listing['permalink'],
			'postedDate'  => gmdate( 'c', $listing['posted_at'] ),
		);
	}
}
