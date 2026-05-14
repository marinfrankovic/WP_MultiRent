<?php
/**
 * Site header.
 *
 * @package MultiRent
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'multirent' ); ?></a>
<div id="page" class="site">
	<header id="masthead" class="site-header">
		<div class="container header-inner">
			<div class="site-branding">
				<?php $page_logo_id = absint( multirent_display_option( 'page_logo', 0 ) ); ?>
				<?php $property_name = multirent_display_option( 'property_name', get_bloginfo( 'name' ) ); ?>
				<?php if ( $page_logo_id ) : ?>
					<a class="multirent-page-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $property_name ); ?>">
						<?php echo wp_get_attachment_image( $page_logo_id, 'medium', false, array( 'class' => 'multirent-page-logo' ) ); ?>
					</a>
				<?php endif; ?>
				<div>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( $property_name ); ?></a></p>
					<?php $description = get_bloginfo( 'description', 'display' ); ?>
					<?php if ( $description ) : ?>
						<p class="site-description"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'multirent' ); ?>">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Menu', 'multirent' ); ?></button>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => 'multirent_default_menu',
					)
				);
				?>
			</nav>
		</div>
	</header>