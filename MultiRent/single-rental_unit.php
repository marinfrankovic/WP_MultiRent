<?php
/**
 * Single rental unit template.
 *
 * @package MultiRent
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?>>
			<section class="unit-hero">
				<div class="container split-section">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'Rental unit', 'multirent' ); ?></p>
						<h1><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', array( 'class' => 'unit-hero-image' ) ); ?>
					<?php endif; ?>
				</div>
			</section>
			<section class="section">
				<div class="container split-section">
					<div class="entry-content"><?php the_content(); ?></div>
					<aside class="unit-details-card">
						<h2><?php esc_html_e( 'Details', 'multirent' ); ?></h2>
						<?php multirent_render_unit_details( get_the_ID() ); ?>
						<?php $booking_url = multirent_unit_detail( get_the_ID(), 'booking_url' ); ?>
						<?php if ( $booking_url ) : ?>
							<a class="button" href="<?php echo esc_url( $booking_url ); ?>"><?php esc_html_e( 'Request this unit', 'multirent' ); ?></a>
						<?php endif; ?>
					</aside>
				</div>
			</section>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();