<?php
/**
 * Elementor widget: registers Content and Style-tab controls, delegates
 * data fetching to Job_Listing_Service. No single-listing template
 * concerns here - see Job_Listing_Post_Type::template_include().
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
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

	public function get_style_depends() {
		return array( 'sagiris-ejo' );
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

		$this->start_controls_section(
			'layout_section',
			array(
				'label' => __( 'Layout', 'sagiris-elementor-job-openings' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'cards_per_row',
			array(
				'label'          => __( 'Cards Per Row', 'sagiris-elementor-job-openings' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors'      => array(
					'{{WRAPPER}} .sagiris-ejo__list' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'appearance_section',
			array(
				'label' => __( 'Appearance', 'sagiris-elementor-job-openings' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'card_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .sagiris-ejo__item',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title Color', 'sagiris-elementor-job-openings' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .sagiris-ejo__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Department / Location Color', 'sagiris-elementor-job-openings' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .sagiris-ejo__meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Title Typography', 'sagiris-elementor-job-openings' ),
				'selector' => '{{WRAPPER}} .sagiris-ejo__title',
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
