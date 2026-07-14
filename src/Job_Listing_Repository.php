<?php
/**
 * Thin, WordPress-dependent fetch: queries `job_listing` posts and
 * normalizes each into the plain array shape Job_Listing_Filter expects.
 * No filtering or sorting logic lives here - see Job_Listing_Filter.
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Job_Listing_Repository {

	public static function get_all(): array {
		$posts = get_posts(
			array(
				'post_type'      => 'job_listing',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		return array_map( array( __CLASS__, 'normalize' ), $posts );
	}

	/**
	 * Public so the single-listing template can normalize the current post
	 * without duplicating this field-extraction logic - deliberately NOT
	 * routed through Job_Listing_Filter, since a listing's own page must
	 * stay viewable after its closing date even though list views exclude
	 * it by default.
	 */
	public static function normalize( \WP_Post $post ): array {
		$closing_date_raw = get_post_meta( $post->ID, '_sagiris_ejo_closing_date', true );

		return array(
			'id'           => $post->ID,
			'title'        => $post->post_title,
			'department'   => (string) get_post_meta( $post->ID, '_sagiris_ejo_department', true ),
			'location'     => (string) get_post_meta( $post->ID, '_sagiris_ejo_location', true ),
			'salary_range' => (string) get_post_meta( $post->ID, '_sagiris_ejo_salary_range', true ),
			'closing_date' => $closing_date_raw ? strtotime( $closing_date_raw . ' 23:59:59' ) : null,
			'apply_url'    => (string) get_post_meta( $post->ID, '_sagiris_ejo_apply_url', true ),
			'description'  => $post->post_content,
			'permalink'    => get_permalink( $post ),
			'posted_at'    => strtotime( $post->post_date_gmt . ' GMT' ),
		);
	}
}
