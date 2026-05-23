<?php
/**
 * Template Name: Apartments - Grid
 *
 * @package MultiRent
 */

get_header();

$rentals = new WP_Query( multirent_current_apartment_page_query_args() );
?>

<main id="primary" class="site-main">
	<header class="page-hero compact-hero apartments-page-hero">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Apartments', 'multirent' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<p><?php esc_html_e( 'Browse the available rental units, compare details, then open each apartment page for photos, amenities, and booking information.', 'multirent' ); ?></p>
		</div>
	</header>

	<section class="section apartments-template-section">
		<div class="container rental-grid">
			<?php if ( $rentals->have_posts() ) : ?>
				<?php while ( $rentals->have_posts() ) : ?>
					<?php $rentals->the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'rental-card' ); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="notice-card">
					<h2><?php esc_html_e( 'Add your first apartments', 'multirent' ); ?></h2>
					<p><?php esc_html_e( 'Open MultiRent Setup > Rental Units and create published rental units to populate this page.', 'multirent' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
