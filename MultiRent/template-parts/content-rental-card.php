<?php
/**
 * Rental card.
 *
 * @package MultiRent
 */

?>
<article <?php post_class( 'rental-card' ); ?>>
	<a href="<?php the_permalink(); ?>" class="rental-card-media" aria-label="<?php the_title_attribute(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'large' ); ?>
		<?php else : ?>
			<div class="placeholder-media"><?php esc_html_e( 'Add photo', 'multirent' ); ?></div>
		<?php endif; ?>
	</a>
	<div class="rental-card-body">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( has_excerpt() ) : ?>
			<p><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
		<?php multirent_render_unit_details( get_the_ID(), true ); ?>
		<a class="text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View details', 'multirent' ); ?></a>
	</div>
</article>