<?php
/**
 * Template Name: Contact / Booking Inquiry
 *
 * @package MultiRent
 */

get_header();

$contact_title = multirent_display_option( 'contact_page_title', __( 'Contact', 'multirent' ) );
$contact_intro = multirent_display_option( 'contact_page_intro', __( 'Send your dates, guest count, and preferred rental unit so the owner can reply with availability.', 'multirent' ) );
$address       = multirent_display_option( 'contact_address', '' );
$phone         = multirent_display_option( 'contact_phone', '' );
$mobile        = multirent_display_option( 'contact_mobile', '' );
$email         = multirent_display_option( 'contact_email', '' );
$form_shortcode = trim( multirent_display_option( 'contact_form_shortcode', '' ) );
$map_query     = trim( multirent_display_option( 'contact_map_query', '' ) );
$map_note      = multirent_display_option( 'contact_map_note', '' );
$booking_lines = multirent_lines_to_items( multirent_display_option( 'booking_help_lines', '' ) );
?>

<main id="primary" class="site-main">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<header class="page-hero compact-hero contact-page-hero">
			<div class="container">
				<p class="eyebrow"><?php esc_html_e( 'Contact', 'multirent' ); ?></p>
				<h1><?php echo esc_html( $contact_title ); ?></h1>
				<?php if ( $contact_intro ) : ?>
					<p><?php echo esc_html( $contact_intro ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<section class="section contact-section">
			<div class="container contact-layout">
				<?php if ( '1' === (string) multirent_display_option( 'show_contact_details', '1' ) ) : ?>
					<aside class="contact-card">
						<h2><?php esc_html_e( 'Contact details', 'multirent' ); ?></h2>
						<?php if ( $address ) : ?>
							<address><?php echo nl2br( esc_html( $address ) ); ?></address>
						<?php endif; ?>
						<ul class="contact-list">
							<?php if ( $phone ) : ?>
								<li><span><?php esc_html_e( 'Phone', 'multirent' ); ?></span><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
							<?php endif; ?>
							<?php if ( $mobile ) : ?>
								<li><span><?php esc_html_e( 'Mobile', 'multirent' ); ?></span><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $mobile ) ); ?>"><?php echo esc_html( $mobile ); ?></a></li>
							<?php endif; ?>
							<?php if ( $email ) : ?>
								<li><span><?php esc_html_e( 'Email', 'multirent' ); ?></span><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li>
							<?php endif; ?>
						</ul>
					</aside>
				<?php endif; ?>

				<div class="contact-form-area">
					<?php if ( '1' === (string) multirent_display_option( 'show_booking_help', '1' ) && $booking_lines ) : ?>
						<section class="booking-help-panel">
							<p class="eyebrow"><?php esc_html_e( 'Booking inquiry', 'multirent' ); ?></p>
							<h2><?php esc_html_e( 'Details that help with a faster reply', 'multirent' ); ?></h2>
							<ul>
								<?php foreach ( $booking_lines as $booking_line ) : ?>
									<li><?php echo esc_html( $booking_line ); ?></li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php endif; ?>

					<?php if ( '1' === (string) multirent_display_option( 'show_contact_map', '1' ) && $map_query ) : ?>
						<div class="contact-map-card">
							<iframe title="<?php esc_attr_e( 'Property map', 'multirent' ); ?>" src="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( $map_query ) . '&output=embed' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
						</div>
					<?php endif; ?>

					<?php if ( '1' === (string) multirent_display_option( 'show_contact_content', '1' ) && trim( get_the_content() ) ) : ?>
						<div class="entry-content content-card"><?php the_content(); ?></div>
					<?php endif; ?>

					<?php if ( '1' === (string) multirent_display_option( 'show_contact_form', '1' ) && $form_shortcode ) : ?>
						<div class="plugin-form content-card"><?php echo do_shortcode( wp_kses_post( $form_shortcode ) ); ?></div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( '1' === (string) multirent_display_option( 'show_contact_map_note', '1' ) && $map_note ) : ?>
				<div class="container">
					<p class="contact-map-note"><?php echo esc_html( $map_note ); ?></p>
				</div>
			<?php endif; ?>
		</section>
	<?php endwhile; ?>
</main>

<?php
get_footer();
