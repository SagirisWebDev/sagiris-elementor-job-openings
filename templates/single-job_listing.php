<?php
/**
 * Default single-listing template. A theme providing its own
 * single-job_listing.php takes priority - see Job_Listing_Post_Type::template_include().
 *
 * @package Sagiris\ElementorJobOpenings
 */

use Sagiris\ElementorJobOpenings\Job_Listing_Repository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$listing    = Job_Listing_Repository::normalize( get_post() );
	$meta_parts = array_filter( array( $listing['department'], $listing['location'] ) );
	?>
	<main class="sagiris-ejo-single">
		<article <?php post_class(); ?>>
			<h1 class="sagiris-ejo-single__title"><?php echo esc_html( $listing['title'] ); ?></h1>

			<?php if ( $meta_parts ) : ?>
				<p class="sagiris-ejo-single__meta"><?php echo esc_html( implode( ' — ', $meta_parts ) ); ?></p>
			<?php endif; ?>

			<?php if ( $listing['salary_range'] ) : ?>
				<p class="sagiris-ejo-single__salary"><?php echo esc_html( $listing['salary_range'] ); ?></p>
			<?php endif; ?>

			<?php if ( $listing['closing_date'] ) : ?>
				<p class="sagiris-ejo-single__closing">
					<?php
					printf(
						/* translators: %s: closing date */
						esc_html__( 'Applications close: %s', 'sagiris-elementor-job-openings' ),
						esc_html( date_i18n( get_option( 'date_format' ), $listing['closing_date'] ) )
					);
					?>
				</p>
			<?php endif; ?>

			<div class="sagiris-ejo-single__description">
				<?php the_content(); ?>
			</div>

			<?php if ( $listing['apply_url'] ) : ?>
				<p class="sagiris-ejo-single__apply">
					<a class="sagiris-ejo-single__apply-button" href="<?php echo esc_url( $listing['apply_url'] ); ?>">
						<?php esc_html_e( 'Apply Now', 'sagiris-elementor-job-openings' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</article>
	</main>
	<?php
endwhile;

get_footer();
