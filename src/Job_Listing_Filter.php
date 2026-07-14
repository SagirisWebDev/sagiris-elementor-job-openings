<?php
/**
 * Pure filter/sort module: normalized job-listing data in, filtered and
 * sorted data out. Deliberately free of WordPress function calls (including
 * time functions - "now" is always passed in) so it can be unit tested in
 * isolation without a WordPress bootstrap. Shared by the REST controller,
 * the GraphQL resolver, and the Elementor widget's render() - see
 * tests/JobListingFilterTest.php.
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

class Job_Listing_Filter {

	/**
	 * @param array<int, array{id?: int, department?: string, location?: string, closing_date?: int, posted_at?: int}> $listings
	 * @param array{department?: string, location?: string, include_expired?: bool, sort?: string} $args
	 */
	public static function apply( array $listings, array $args, int $now ): array {
		$department      = isset( $args['department'] ) && '' !== $args['department'] ? (string) $args['department'] : null;
		$location        = isset( $args['location'] ) && '' !== $args['location'] ? (string) $args['location'] : null;
		$include_expired = ! empty( $args['include_expired'] );
		$sort            = isset( $args['sort'] ) && 'closing_soon' === $args['sort'] ? 'closing_soon' : 'newest';

		$filtered = array_values(
			array_filter(
				$listings,
				static function ( array $listing ) use ( $department, $location, $include_expired, $now ) {
					if ( null !== $department && ( $listing['department'] ?? '' ) !== $department ) {
						return false;
					}

					if ( null !== $location && ( $listing['location'] ?? '' ) !== $location ) {
						return false;
					}

					if ( ! $include_expired && isset( $listing['closing_date'] ) && $listing['closing_date'] < $now ) {
						return false;
					}

					return true;
				}
			)
		);

		usort(
			$filtered,
			static function ( array $a, array $b ) use ( $sort ) {
				if ( 'closing_soon' === $sort ) {
					return ( $a['closing_date'] ?? PHP_INT_MAX ) <=> ( $b['closing_date'] ?? PHP_INT_MAX );
				}

				return ( $b['posted_at'] ?? 0 ) <=> ( $a['posted_at'] ?? 0 );
			}
		);

		return $filtered;
	}
}
