<?php
/**
 * Template Name: Apartments - Featured Guide
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
			<p><?php esc_html_e( 'A guided apartment overview for guests who want to compare choices before opening each detailed page.', 'multirent' ); ?></p>
		</div>
	</header>

	<section class="section intro-section">
		<div class="container split-section">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Choose faster', 'multirent' ); ?></p>
				<h2><?php esc_html_e( 'Find the right stay for your trip', 'multirent' ); ?></h2>
			</div>
			<div class="lead-text">
				<p><?php esc_html_e( 'Use this layout when you want the apartments page to feel like the original rental-site overview: clear intro, simple comparison cards, and direct links into each apartment.', 'multirent' ); ?></p>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<?php if ( trim( get_the_content() ) ) : ?>
						<div class="entry-content"><?php the_content(); ?></div>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>
		</div>
	</section>

	<section class="section apartments-template-section">
		<div class="container section-heading">
			<p class="eyebrow"><?php esc_html_e( 'Rental options', 'multirent' ); ?></p>
			<h2><?php esc_html_e( 'Compare available apartments', 'multirent' ); ?></h2>
		</div>
		<div class="container rental-grid">
			<?php if ( $rentals->have_posts() ) : ?>
				<?php while ( $rentals->have_posts() ) : ?>
					<?php $rentals->the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'rental-card' ); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="notice-card">
					<h2><?php esc_html_e( 'No rental units are published yet', 'multirent' ); ?></h2>
					<p><?php esc_html_e( 'Create rental units from MultiRent Setup to fill this apartment guide.', 'multirent' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
