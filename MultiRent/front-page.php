<?php
/**
 * Front page template.
 *
 * @package MultiRent
 */

get_header();

$hero_image_id  = absint( multirent_display_option( 'hero_image', 0 ) );
$hero_image_url = $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'full' ) : '';
$hero_style     = $hero_image_url ? ' style="background-image: linear-gradient(90deg, rgba(7, 31, 54, 0.86), rgba(16, 94, 125, 0.48)), url(' . esc_url( $hero_image_url ) . ');"' : '';
?>

<main id="primary" class="site-main">
	<section class="hero"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="container hero-inner">
			<p class="eyebrow"><?php esc_html_e( 'Multi-unit rental website', 'multirent' ); ?></p>
			<h1><?php echo esc_html( multirent_display_option( 'hero_title', __( 'Flexible stays for every guest', 'multirent' ) ) ); ?></h1>
			<p><?php echo esc_html( multirent_display_option( 'hero_text', __( 'Showcase apartments, rooms, villas, or holiday homes with clear details and easy inquiry paths.', 'multirent' ) ) ); ?></p>
			<a class="button" href="<?php echo esc_url( multirent_display_option( 'hero_button_url', '#rentals' ) ); ?>"><?php echo esc_html( multirent_display_option( 'hero_button_text', __( 'View rentals', 'multirent' ) ) ); ?></a>
		</div>
	</section>

	<section class="section intro-section">
		<div class="container split-section">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'GUI managed', 'multirent' ); ?></p>
				<h2><?php echo esc_html( multirent_display_option( 'intro_title', __( 'Manage every unit from WordPress', 'multirent' ) ) ); ?></h2>
			</div>
			<div class="lead-text">
				<p><?php echo esc_html( multirent_display_option( 'intro_text', __( 'Add rental units, amenities, photos, capacity, booking links, and guest-facing details from the WordPress dashboard.', 'multirent' ) ) ); ?></p>
			</div>
		</div>
	</section>

	<?php $stats = multirent_stats(); ?>
	<?php if ( $stats ) : ?>
		<section class="stats-strip" aria-label="<?php esc_attr_e( 'Property highlights', 'multirent' ); ?>">
			<div class="container stats-grid">
				<?php foreach ( $stats as $stat ) : ?>
					<div class="stat-item">
						<strong><?php echo esc_html( $stat['value'] ); ?></strong>
						<span><?php echo esc_html( $stat['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( '1' === (string) multirent_display_option( 'show_front_page_rentals', '1' ) ) : ?>
		<section id="rentals" class="section rentals-section">
			<div class="container section-heading">
				<p class="eyebrow"><?php esc_html_e( 'Rental units', 'multirent' ); ?></p>
				<h2><?php esc_html_e( 'Choose what fits your stay', 'multirent' ); ?></h2>
			</div>
			<?php if ( post_type_exists( 'rental_unit' ) ) : ?>
			<?php $front_page_rental_count = min( 50, max( 1, absint( multirent_display_option( 'front_page_rental_count', 12 ) ) ) ); ?>
			<div class="container rental-grid">
				<?php
				$units = new WP_Query(
					array(
						'post_type'      => 'rental_unit',
						'posts_per_page' => $front_page_rental_count,
						'orderby'        => 'menu_order title',
						'order'          => 'ASC',
					)
				);
				?>
				<?php if ( $units->have_posts() ) : ?>
					<?php
					while ( $units->have_posts() ) :
						$units->the_post();
						get_template_part( 'template-parts/content', 'rental-card' );
					endwhile;
					wp_reset_postdata();
					?>
				<?php else : ?>
					<div class="notice-card">
						<h3><?php esc_html_e( 'Create your first rentals', 'multirent' ); ?></h3>
						<p><?php esc_html_e( 'Open MultiRent Setup in WordPress admin and choose how many apartments, rooms, or villas you want to create.', 'multirent' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
			<?php else : ?>
			<div class="container notice-card">
				<h3><?php esc_html_e( 'MultiRent Companion plugin required', 'multirent' ); ?></h3>
				<p><?php esc_html_e( 'Activate the companion plugin to manage rental units, amenities, setup wizard, and demo content from the WordPress dashboard.', 'multirent' ); ?></p>
			</div>
			<?php endif; ?>
		</section>
	<?php endif; ?>

	<?php $reviews_shortcode = trim( multirent_display_option( 'reviews_shortcode', '' ) ); ?>
	<?php if ( '1' === (string) multirent_display_option( 'show_reviews', '0' ) && $reviews_shortcode ) : ?>
		<section class="section reviews-section">
			<div class="container section-heading">
				<p class="eyebrow"><?php esc_html_e( 'Guest reviews', 'multirent' ); ?></p>
			</div>
			<div class="container reviews-widget">
				<?php echo do_shortcode( wp_kses_post( $reviews_shortcode ) ); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( current_user_can( 'manage_options' ) && ( '1' === (string) multirent_display_option( 'show_seo_note', '0' ) || '1' === (string) multirent_display_option( 'show_migration_note', '0' ) ) ) : ?>
		<section class="section admin-reminder-section">
			<div class="container notice-card">
				<h2><?php esc_html_e( 'Admin reminders', 'multirent' ); ?></h2>
				<?php if ( '1' === (string) multirent_display_option( 'show_seo_note', '0' ) ) : ?>
					<p><?php esc_html_e( 'Configure your SEO plugin titles, descriptions, sitemap, and schema after editing property content.', 'multirent' ); ?></p>
				<?php endif; ?>
				<?php if ( '1' === (string) multirent_display_option( 'show_migration_note', '0' ) ) : ?>
					<p><?php esc_html_e( 'Create a full backup before importing, migrating, or making large content changes.', 'multirent' ); ?></p>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<section id="contact" class="section cta-section">
		<div class="container cta-panel">
			<div>
				<h2><?php echo esc_html( multirent_display_option( 'contact_title', __( 'Ready to receive inquiries?', 'multirent' ) ) ); ?></h2>
				<p><?php echo esc_html( multirent_display_option( 'contact_text', __( 'Connect this section to your contact page, booking form, or external reservation system.', 'multirent' ) ) ); ?></p>
			</div>
			<a class="button button-light" href="<?php echo esc_url( multirent_display_option( 'contact_button_url', '#contact' ) ); ?>"><?php echo esc_html( multirent_display_option( 'contact_button_text', __( 'Contact us', 'multirent' ) ) ); ?></a>
		</div>
	</section>
</main>

<?php
get_footer();