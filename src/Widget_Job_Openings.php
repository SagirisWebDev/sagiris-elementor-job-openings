<?php
/**
 * Elementor widget: registers Content-tab controls, delegates data fetching
 * to Job_Listing_Service. No Style-tab controls or single-listing template
 * yet - those are later slices.
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget_Job_Openings extends Widget_Base {

	public function get_name() {
		return 'sagiris-job-openings';
	}

	public function get_title() {
		return __( 'Job Openings', 'sagiris-elementor-job-openings' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return array( 'general' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Job Openings', 'sagiris-elementor-job-openings' ),
			)
		);

		$this->add_control(
			'department',
			array(
				'label'       => __( 'Department', 'sagiris-elementor-job-openings' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'All departments', 'sagiris-elementor-job-openings' ),
			)
		);

		$this->add_control(
			'location',
			array(
				'label'       => __( 'Location', 'sagiris-elementor-job-openings' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'All locations', 'sagiris-elementor-job-openings' ),
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => __( 'Number to Show', 'sagiris-elementor-job-openings' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 1,
			)
		);

		$this->add_control(
			'sort',
			array(
				'label'   => __( 'Sort By', 'sagiris-elementor-job-openings' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'newest',
				'options' => array(
					'newest'       => __( 'Newest First', 'sagiris-elementor-job-openings' ),
					'closing_soon' => __( 'Closing Soonest', 'sagiris-elementor-job-openings' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$listings = Job_Listing_Service::query(
			array(
				'department' => $settings['department'] ?? '',
				'location'   => $settings['location'] ?? '',
				'sort'       => $settings['sort'] ?? 'newest',
			)
		);

		$count    = isset( $settings['count'] ) ? max( 1, (int) $settings['count'] ) : 10;
		$listings = array_slice( $listings, 0, $count );

		if ( empty( $listings ) ) {
			echo '<p class="sagiris-ejo__empty">' . esc_html__( 'No current openings.', 'sagiris-elementor-job-openings' ) . '</p>';
			return;
		}

		echo '<ul class="sagiris-ejo__list">';
		foreach ( $listings as $listing ) {
			$meta = array_filter( array( $listing['department'], $listing['location'] ) );

			echo '<li class="sagiris-ejo__item">';
			printf(
				'<a class="sagiris-ejo__title" href="%1$s">%2$s</a>',
				esc_url( $listing['permalink'] ),
				esc_html( $listing['title'] )
			);

			if ( $meta ) {
				echo '<span class="sagiris-ejo__meta">' . esc_html( implode( ' — ', $meta ) ) . '</span>';
			}

			echo '</li>';
		}
		echo '</ul>';
	}
}
