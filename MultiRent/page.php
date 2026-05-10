<?php
/**
 * Page template.
 *
 * @package MultiRent
 */

get_header();
?>
<main id="primary" class="site-main section">
	<div class="container narrow-content">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();