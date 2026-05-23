<?php
/**
 * Template Name: Apartments - Compact List
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
			<p><?php esc_html_e( 'A tighter apartment listing for rental sites with many units or guests who need quick comparison.', 'multirent' ); ?></p>
		</div>
	</header>

	<section class="section apartments-template-section">
		<div class="container compact-rental-list">
			<?php if ( $rentals->have_posts() ) : ?>
				<?php while ( $rentals->have_posts() ) : ?>
					<?php $rentals->the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'rental-card' ); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="notice-card">
					<h2><?php esc_html_e( 'No rental units are published yet', 'multirent' ); ?></h2>
					<p><?php esc_html_e( 'Create rental units from MultiRent Setup to fill this compact list.', 'multirent' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
