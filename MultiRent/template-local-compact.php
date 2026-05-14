<?php
/**
 * Template Name: Local - Compact Guide
 *
 * @package MultiRent
 */

get_header();

$local_title      = multirent_display_option( 'local_page_title', __( 'Local information', 'multirent' ) );
$local_intro      = multirent_display_option( 'local_page_intro', __( 'Help guests plan arrival, beaches, restaurants, activities, and useful services around your property.', 'multirent' ) );
$local_guides     = multirent_lines_to_cards( multirent_display_option( 'local_guide_lines', '' ) );
$local_highlights = multirent_lines_to_cards( multirent_display_option( 'local_highlight_lines', '' ) );
$local_activities = multirent_lines_to_cards( multirent_display_option( 'local_activity_lines', '' ) );
$local_links      = multirent_lines_to_links( multirent_display_option( 'local_link_lines', '' ) );
?>

<main id="primary" class="site-main">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<header class="page-hero compact-hero local-page-hero">
			<div class="container narrow-content">
				<p class="eyebrow"><?php esc_html_e( 'Local', 'multirent' ); ?></p>
				<h1><?php echo esc_html( $local_title ); ?></h1>
				<?php if ( $local_intro ) : ?>
					<p><?php echo esc_html( $local_intro ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<section class="section local-section local-template-compact">
			<div class="container narrow-content local-compact-stack">
				<?php if ( '1' === (string) multirent_display_option( 'show_local_guides', '1' ) && $local_guides ) : ?>
					<section class="local-guide-panel">
						<p class="eyebrow"><?php esc_html_e( 'Plan your stay', 'multirent' ); ?></p>
						<div class="local-guide-grid">
							<?php foreach ( $local_guides as $local_guide ) : ?>
								<section>
									<h3><?php echo esc_html( $local_guide['title'] ); ?></h3>
									<p><?php echo esc_html( $local_guide['text'] ); ?></p>
								</section>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>

				<?php if ( '1' === (string) multirent_display_option( 'show_local_highlights', '1' ) && $local_highlights ) : ?>
					<div class="local-highlight-grid">
						<?php foreach ( $local_highlights as $highlight ) : ?>
							<section class="local-info-card">
								<h2><?php echo esc_html( $highlight['title'] ); ?></h2>
								<p><?php echo esc_html( $highlight['text'] ); ?></p>
							</section>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( '1' === (string) multirent_display_option( 'show_local_activities', '1' ) && $local_activities ) : ?>
					<section class="local-activity-panel">
						<p class="eyebrow"><?php esc_html_e( 'Explore', 'multirent' ); ?></p>
						<h2><?php esc_html_e( 'Trips and activities', 'multirent' ); ?></h2>
						<div class="local-activity-grid">
							<?php foreach ( $local_activities as $activity ) : ?>
								<section class="local-info-card local-activity-card">
									<h3><?php echo esc_html( $activity['title'] ); ?></h3>
									<p><?php echo esc_html( $activity['text'] ); ?></p>
								</section>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>

				<?php if ( '1' === (string) multirent_display_option( 'show_local_links', '1' ) && $local_links ) : ?>
					<section class="local-links-panel">
						<p class="eyebrow"><?php esc_html_e( 'Travel links', 'multirent' ); ?></p>
						<h2><?php esc_html_e( 'Useful connections', 'multirent' ); ?></h2>
						<ul class="local-link-list">
							<?php foreach ( $local_links as $local_link ) : ?>
								<li><a href="<?php echo esc_url( $local_link['url'] ); ?>" rel="noopener noreferrer"><?php echo esc_html( $local_link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( '1' === (string) multirent_display_option( 'show_local_content', '1' ) && trim( get_the_content() ) ) : ?>
					<div class="entry-content content-card"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>
		</section>
	<?php endwhile; ?>
</main>

<?php
get_footer();
