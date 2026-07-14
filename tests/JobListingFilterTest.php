<?php

namespace Sagiris\ElementorJobOpenings\Tests;

use PHPUnit\Framework\TestCase;
use Sagiris\ElementorJobOpenings\Job_Listing_Filter;

final class JobListingFilterTest extends TestCase {

	private const NOW = 1_700_000_000;

	public function test_filters_by_department_only(): void {
		$listings = array(
			array(
				'id'         => 1,
				'department' => 'Engineering',
			),
			array(
				'id'         => 2,
				'department' => 'Sales',
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array( 'department' => 'Engineering' ), self::NOW );

		$this->assertCount( 1, $result );
		$this->assertSame( 1, $result[0]['id'] );
	}

	public function test_filters_by_location_only(): void {
		$listings = array(
			array(
				'id'       => 1,
				'location' => 'Remote',
			),
			array(
				'id'       => 2,
				'location' => 'Austin',
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array( 'location' => 'Austin' ), self::NOW );

		$this->assertCount( 1, $result );
		$this->assertSame( 2, $result[0]['id'] );
	}

	public function test_filters_by_department_and_location_combined(): void {
		$listings = array(
			array(
				'id'         => 1,
				'department' => 'Engineering',
				'location'   => 'Remote',
			),
			array(
				'id'         => 2,
				'department' => 'Engineering',
				'location'   => 'Austin',
			),
			array(
				'id'         => 3,
				'department' => 'Sales',
				'location'   => 'Remote',
			),
		);

		$result = Job_Listing_Filter::apply(
			$listings,
			array(
				'department' => 'Engineering',
				'location'   => 'Remote',
			),
			self::NOW
		);

		$this->assertCount( 1, $result );
		$this->assertSame( 1, $result[0]['id'] );
	}

	public function test_returns_everything_when_department_and_location_are_omitted(): void {
		$listings = array(
			array(
				'id'         => 1,
				'department' => 'Engineering',
				'location'   => 'Remote',
			),
			array(
				'id'         => 2,
				'department' => 'Sales',
				'location'   => 'Austin',
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array(), self::NOW );

		$this->assertCount( 2, $result );
	}

	public function test_excludes_expired_listings_by_default(): void {
		$listings = array(
			array(
				'id'           => 1,
				'closing_date' => self::NOW - 100,
			),
			array(
				'id'           => 2,
				'closing_date' => self::NOW + 100,
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array(), self::NOW );

		$this->assertCount( 1, $result );
		$this->assertSame( 2, $result[0]['id'] );
	}

	public function test_include_expired_overrides_default_exclusion(): void {
		$listings = array(
			array(
				'id'           => 1,
				'closing_date' => self::NOW - 100,
			),
			array(
				'id'           => 2,
				'closing_date' => self::NOW + 100,
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array( 'include_expired' => true ), self::NOW );

		$this->assertCount( 2, $result );
	}

	public function test_listing_with_no_closing_date_never_expires(): void {
		$listings = array(
			array( 'id' => 1 ),
		);

		$result = Job_Listing_Filter::apply( $listings, array(), self::NOW );

		$this->assertCount( 1, $result );
	}

	public function test_closing_date_exactly_now_is_still_open(): void {
		$listings = array(
			array(
				'id'           => 1,
				'closing_date' => self::NOW,
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array(), self::NOW );

		$this->assertCount( 1, $result );
	}

	public function test_closing_date_one_second_before_now_is_expired(): void {
		$listings = array(
			array(
				'id'           => 1,
				'closing_date' => self::NOW - 1,
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array(), self::NOW );

		$this->assertCount( 0, $result );
	}

	public function test_closing_date_one_second_after_now_is_open(): void {
		$listings = array(
			array(
				'id'           => 1,
				'closing_date' => self::NOW + 1,
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array(), self::NOW );

		$this->assertCount( 1, $result );
	}

	public function test_sorts_newest_first_by_default(): void {
		$listings = array(
			array(
				'id'        => 1,
				'posted_at' => self::NOW - 1000,
			),
			array(
				'id'        => 2,
				'posted_at' => self::NOW - 100,
			),
			array(
				'id'        => 3,
				'posted_at' => self::NOW - 5000,
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array(), self::NOW );

		$this->assertSame( array( 2, 1, 3 ), array_column( $result, 'id' ) );
	}

	public function test_sorts_by_closing_soonest_when_requested(): void {
		$listings = array(
			array(
				'id'           => 1,
				'closing_date' => self::NOW + 5000,
			),
			array(
				'id'           => 2,
				'closing_date' => self::NOW + 100,
			),
			array(
				'id'           => 3,
				'closing_date' => self::NOW + 1000,
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array( 'sort' => 'closing_soon' ), self::NOW );

		$this->assertSame( array( 2, 3, 1 ), array_column( $result, 'id' ) );
	}

	public function test_closing_soon_sort_places_never_expiring_listings_last(): void {
		$listings = array(
			array( 'id' => 1 ),
			array(
				'id'           => 2,
				'closing_date' => self::NOW + 100,
			),
		);

		$result = Job_Listing_Filter::apply( $listings, array( 'sort' => 'closing_soon' ), self::NOW );

		$this->assertSame( array( 2, 1 ), array_column( $result, 'id' ) );
	}
}
