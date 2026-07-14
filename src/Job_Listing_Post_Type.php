<?php
/**
 * Registers the `job_listing` custom post type and its meta fields
 * (department, location, salary_range, closing_date, apply_url), authored
 * via a classic meta box.
 *
 * @package Sagiris\ElementorJobOpenings
 */

namespace Sagiris\ElementorJobOpenings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Job_Listing_Post_Type {

	const META_FIELDS = array( 'department', 'location', 'salary_range', 'closing_date', 'apply_url' );

	public static function register(): void {
		register_post_type(
			'job_listing',
			array(
				'labels'      => array(
					'name'          => __( 'Job Listings', 'sagiris-elementor-job-openings' ),
					'singular_name' => __( 'Job Listing', 'sagiris-elementor-job-openings' ),
					'add_new_item'  => __( 'Add New Job Listing', 'sagiris-elementor-job-openings' ),
					'edit_item'     => __( 'Edit Job Listing', 'sagiris-elementor-job-openings' ),
					'all_items'     => __( 'Job Listings', 'sagiris-elementor-job-openings' ),
				),
				'public'      => true,
				'has_archive' => false,
				'rewrite'     => array( 'slug' => 'careers' ),
				'supports'    => array( 'title', 'editor' ),
				'menu_icon'   => 'dashicons-portfolio',
			)
		);

		foreach ( self::META_FIELDS as $field ) {
			register_post_meta(
				'job_listing',
				'_sagiris_ejo_' . $field,
				array(
					'type'              => 'string',
					'single'            => true,
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
		}
	}

	public static function register_meta_box(): void {
		add_meta_box(
			'sagiris_ejo_details',
			__( 'Job Listing Details', 'sagiris-elementor-job-openings' ),
			array( __CLASS__, 'render_meta_box' ),
			'job_listing',
			'normal',
			'high'
		);
	}

	public static function render_meta_box( \WP_Post $post ): void {
		wp_nonce_field( 'sagiris_ejo_save_meta', 'sagiris_ejo_meta_nonce' );

		$values = array();
		foreach ( self::META_FIELDS as $field ) {
			$values[ $field ] = get_post_meta( $post->ID, '_sagiris_ejo_' . $field, true );
		}
		?>
		<p>
			<label for="sagiris_ejo_department"><?php esc_html_e( 'Department', 'sagiris-elementor-job-openings' ); ?></label><br />
			<input type="text" id="sagiris_ejo_department" name="sagiris_ejo_department" class="widefat" value="<?php echo esc_attr( $values['department'] ); ?>" />
		</p>
		<p>
			<label for="sagiris_ejo_location"><?php esc_html_e( 'Location', 'sagiris-elementor-job-openings' ); ?></label><br />
			<input type="text" id="sagiris_ejo_location" name="sagiris_ejo_location" class="widefat" value="<?php echo esc_attr( $values['location'] ); ?>" />
		</p>
		<p>
			<label for="sagiris_ejo_salary_range"><?php esc_html_e( 'Salary Range', 'sagiris-elementor-job-openings' ); ?></label><br />
			<input type="text" id="sagiris_ejo_salary_range" name="sagiris_ejo_salary_range" class="widefat" placeholder="$80k-$100k" value="<?php echo esc_attr( $values['salary_range'] ); ?>" />
		</p>
		<p>
			<label for="sagiris_ejo_closing_date"><?php esc_html_e( 'Closing Date', 'sagiris-elementor-job-openings' ); ?></label><br />
			<input type="date" id="sagiris_ejo_closing_date" name="sagiris_ejo_closing_date" value="<?php echo esc_attr( $values['closing_date'] ); ?>" />
		</p>
		<p>
			<label for="sagiris_ejo_apply_url"><?php esc_html_e( 'Apply URL or mailto: address', 'sagiris-elementor-job-openings' ); ?></label><br />
			<input type="text" id="sagiris_ejo_apply_url" name="sagiris_ejo_apply_url" class="widefat" placeholder="https://... or mailto:jobs@example.com" value="<?php echo esc_attr( $values['apply_url'] ); ?>" />
		</p>
		<?php
	}

	public static function save_meta( int $post_id ): void {
		if ( ! isset( $_POST['sagiris_ejo_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sagiris_ejo_meta_nonce'] ) ), 'sagiris_ejo_save_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( self::META_FIELDS as $field ) {
			if ( isset( $_POST[ 'sagiris_ejo_' . $field ] ) ) {
				update_post_meta(
					$post_id,
					'_sagiris_ejo_' . $field,
					sanitize_text_field( wp_unslash( $_POST[ 'sagiris_ejo_' . $field ] ) )
				);
			}
		}
	}
}
