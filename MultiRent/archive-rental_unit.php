<?php
/**
 * Rental unit archive.
 *
 * @package MultiRent
 */

get_header();
?>
<main id="primary" class="site-main section rentals-section">
	<div class="container section-heading">
		<p class="eyebrow"><?php esc_html_e( 'Rental units', 'multirent' ); ?></p>
		<h1><?php post_type_archive_title(); ?></h1>
	</div>
	<div class="container rental-grid">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'rental-card' ); ?>
			<?php endwhile; ?>
			<?php the_posts_navigation(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No rental units are published yet.', 'multirent' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();