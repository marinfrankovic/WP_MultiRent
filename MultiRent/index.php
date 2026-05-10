<?php
/**
 * Main template.
 *
 * @package MultiRent
 */

get_header();
?>
<main id="primary" class="site-main section">
	<div class="container content-list">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'content-card' ); ?>>
					<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_navigation(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No content has been published yet.', 'multirent' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();