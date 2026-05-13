<?php
/**
 * Multi Apartment Rental theme bootstrap.
 *
 * @package MultiRent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MULTIRENT_VERSION', '0.1.15' );
define( 'MULTIRENT_DIR', get_template_directory() );
define( 'MULTIRENT_URI', get_template_directory_uri() );

function multirent_setup() {
	load_theme_textdomain( 'multirent', MULTIRENT_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array( 'height' => 90, 'width' => 260, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary menu', 'multirent' ),
			'footer'  => esc_html__( 'Footer menu', 'multirent' ),
		)
	);
}
add_action( 'after_setup_theme', 'multirent_setup' );

function multirent_enqueue_assets() {
	wp_enqueue_style( 'multirent-theme', MULTIRENT_URI . '/assets/css/theme.css', array(), MULTIRENT_VERSION );
	wp_add_inline_style( 'multirent-theme', multirent_custom_color_css() );
	wp_enqueue_script( 'multirent-navigation', MULTIRENT_URI . '/assets/js/navigation.js', array(), MULTIRENT_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'multirent_enqueue_assets' );

function multirent_default_menu() {
	?>
	<ul id="primary-menu" class="menu">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'multirent' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/rentals/' ) ); ?>"><?php esc_html_e( 'Rentals', 'multirent' ); ?></a></li>
		<li><a href="<?php echo esc_url( multirent_display_option( 'contact_button_url', '#contact' ) ); ?>"><?php esc_html_e( 'Contact', 'multirent' ); ?></a></li>
	</ul>
	<?php
}

function multirent_plugin_settings() {
	$settings = get_option( 'multirent_settings', array() );
	return is_array( $settings ) ? $settings : array();
}

function multirent_display_option( $key, $default = '' ) {
	$settings = multirent_plugin_settings();
	if ( isset( $settings[ $key ] ) && '' !== $settings[ $key ] ) {
		return $settings[ $key ];
	}

	return multirent_option( $key, $default );
}

function multirent_color_schemes() {
	return array(
		'coastal' => array(
			'label'    => esc_html__( 'Coastal Blue', 'multirent' ),
			'ink'      => '#102033',
			'muted'    => '#5d7088',
			'primary'  => '#087ea4',
			'dark'     => '#06364d',
			'surface'  => '#f8fbfd',
			'accent'   => '#dff3fb',
		),
		'olive'   => array(
			'label'    => esc_html__( 'Olive Garden', 'multirent' ),
			'ink'      => '#1d2b22',
			'muted'    => '#617164',
			'primary'  => '#4f7b45',
			'dark'     => '#223d29',
			'surface'  => '#f8faf4',
			'accent'   => '#e5efd8',
		),
		'coral'   => array(
			'label'    => esc_html__( 'Coral Sunset', 'multirent' ),
			'ink'      => '#2c2630',
			'muted'    => '#766a72',
			'primary'  => '#c7524a',
			'dark'     => '#4b2830',
			'surface'  => '#fff8f5',
			'accent'   => '#ffe1d8',
		),
		'graphite' => array(
			'label'    => esc_html__( 'Graphite', 'multirent' ),
			'ink'      => '#171d24',
			'muted'    => '#5e6875',
			'primary'  => '#3d6f86',
			'dark'     => '#202a34',
			'surface'  => '#f6f7f8',
			'accent'   => '#dce6eb',
		),
	);
}

function multirent_color_tokens() {
	$scheme_key = multirent_display_option( 'color_scheme', 'coastal' );
	$schemes    = multirent_color_schemes();
	$tokens     = isset( $schemes[ $scheme_key ] ) ? $schemes[ $scheme_key ] : $schemes['coastal'];

	$use_custom_colors = '1' === (string) multirent_display_option( 'use_custom_colors', '0' );
	if ( $use_custom_colors ) {
		foreach ( array( 'ink', 'muted', 'primary', 'dark', 'surface', 'accent' ) as $key ) {
			$custom = multirent_display_option( 'color_' . $key, '' );
			if ( $custom && sanitize_hex_color( $custom ) ) {
				$tokens[ $key ] = sanitize_hex_color( $custom );
			}
		}
	}

	return $tokens;
}

function multirent_custom_color_css() {
	$tokens = multirent_color_tokens();
	return sprintf(
		':root{--ink:%1$s;--muted:%2$s;--sea:%3$s;--sea-dark:%4$s;--paper:%5$s;--sky:%6$s;}',
		esc_html( $tokens['ink'] ),
		esc_html( $tokens['muted'] ),
		esc_html( $tokens['primary'] ),
		esc_html( $tokens['dark'] ),
		esc_html( $tokens['surface'] ),
		esc_html( $tokens['accent'] )
	);
}

function multirent_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'multirent_home',
		array(
			'title'       => esc_html__( 'Multi Apartment Rental Setup', 'multirent' ),
			'description' => esc_html__( 'Configure the homepage, property details, reviews widget, and contact call to action without editing code.', 'multirent' ),
			'priority'    => 30,
		)
	);

	$settings = array(
		'property_name'       => array( 'label' => esc_html__( 'Property name', 'multirent' ), 'default' => esc_html__( 'Your Rental Property', 'multirent' ), 'type' => 'text' ),
		'hero_title'          => array( 'label' => esc_html__( 'Hero title', 'multirent' ), 'default' => esc_html__( 'Flexible stays for every guest', 'multirent' ), 'type' => 'text' ),
		'hero_text'           => array( 'label' => esc_html__( 'Hero text', 'multirent' ), 'default' => esc_html__( 'Showcase apartments, rooms, villas, or holiday homes with clear details and easy inquiry paths.', 'multirent' ), 'type' => 'textarea' ),
		'hero_button_text'    => array( 'label' => esc_html__( 'Hero button text', 'multirent' ), 'default' => esc_html__( 'View rentals', 'multirent' ), 'type' => 'text' ),
		'hero_button_url'     => array( 'label' => esc_html__( 'Hero button URL', 'multirent' ), 'default' => '#rentals', 'type' => 'url' ),
		'intro_title'         => array( 'label' => esc_html__( 'Intro title', 'multirent' ), 'default' => esc_html__( 'Manage every unit from WordPress', 'multirent' ), 'type' => 'text' ),
		'intro_text'          => array( 'label' => esc_html__( 'Intro text', 'multirent' ), 'default' => esc_html__( 'Add rental units, amenities, photos, capacity, booking links, and guest-facing details from the WordPress dashboard.', 'multirent' ), 'type' => 'textarea' ),
		'stats_lines'         => array( 'label' => esc_html__( 'Homepage stats, one per line: value | label', 'multirent' ), 'default' => "4 | Rental units\n100 m | Example distance\n24/7 | Self-managed content", 'type' => 'textarea' ),
		'reviews_shortcode'   => array( 'label' => esc_html__( 'Reviews shortcode', 'multirent' ), 'default' => '', 'type' => 'text' ),
		'contact_title'       => array( 'label' => esc_html__( 'Contact title', 'multirent' ), 'default' => esc_html__( 'Ready to receive inquiries?', 'multirent' ), 'type' => 'text' ),
		'contact_text'        => array( 'label' => esc_html__( 'Contact text', 'multirent' ), 'default' => esc_html__( 'Connect this section to your contact page, booking form, or external reservation system.', 'multirent' ), 'type' => 'textarea' ),
		'contact_button_text' => array( 'label' => esc_html__( 'Contact button text', 'multirent' ), 'default' => esc_html__( 'Contact us', 'multirent' ), 'type' => 'text' ),
		'contact_button_url'  => array( 'label' => esc_html__( 'Contact button URL', 'multirent' ), 'default' => '#contact', 'type' => 'url' ),
		'color_scheme'        => array( 'label' => esc_html__( 'Color scheme', 'multirent' ), 'default' => 'coastal', 'type' => 'select' ),
		'use_custom_colors'   => array( 'label' => esc_html__( 'Use custom colors', 'multirent' ), 'default' => '0', 'type' => 'checkbox' ),
	);

	foreach ( $settings as $key => $config ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => 'url' === $config['type'] ? 'esc_url_raw' : 'sanitize_textarea_field',
			)
		);
		$control_args = array(
			'label'   => $config['label'],
			'section' => 'multirent_home',
			'type'    => $config['type'],
		);

		if ( 'color_scheme' === $key ) {
			$control_args['choices'] = wp_list_pluck( multirent_color_schemes(), 'label' );
		}

		$wp_customize->add_control( $key, $control_args );
	}

	foreach ( array( 'primary', 'dark', 'surface', 'accent' ) as $color_key ) {
		$wp_customize->add_setting(
			'color_' . $color_key,
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_' . $color_key, array( 'label' => sprintf( esc_html__( 'Custom %s color', 'multirent' ), $color_key ), 'section' => 'multirent_home' ) ) );
	}

	$wp_customize->add_setting( 'hero_image', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'hero_image', array( 'label' => esc_html__( 'Hero image', 'multirent' ), 'section' => 'multirent_home', 'mime_type' => 'image' ) ) );
}
add_action( 'customize_register', 'multirent_customize_register' );

function multirent_option( $key, $default = '' ) {
	$value = get_theme_mod( $key, $default );
	return is_string( $value ) ? $value : $default;
}

function multirent_stats() {
	$lines = preg_split( '/\r\n|\r|\n/', multirent_display_option( 'stats_lines', "4 | Rental units\n100 m | Example distance\n24/7 | Self-managed content" ) );
	$stats = array();
	foreach ( $lines as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$stats[] = array( 'value' => $parts[0], 'label' => $parts[1] );
		}
	}
	return $stats;
}

function multirent_lines_to_items( $text ) {
	$items = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' !== $line ) {
			$items[] = $line;
		}
	}

	return $items;
}

function multirent_lines_to_cards( $text ) {
	$cards = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	foreach ( $lines as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$cards[] = array( 'title' => $parts[0], 'text' => $parts[1] );
		}
	}

	return $cards;
}

function multirent_lines_to_links( $text ) {
	$links = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	foreach ( $lines as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$links[] = array( 'label' => $parts[0], 'url' => $parts[1] );
		}
	}

	return $links;
}

function multirent_unit_detail( $post_id, $key ) {
	return get_post_meta( $post_id, '_' . $key, true );
}

function multirent_unit_detail_labels() {
	return array(
		'capacity'   => esc_html__( 'Guests', 'multirent' ),
		'bedrooms'   => esc_html__( 'Bedrooms', 'multirent' ),
		'bathrooms'  => esc_html__( 'Bathrooms', 'multirent' ),
		'size'       => esc_html__( 'Size', 'multirent' ),
		'distance'   => esc_html__( 'Location', 'multirent' ),
		'price_note' => esc_html__( 'Price', 'multirent' ),
	);
}

function multirent_render_unit_details( $post_id, $compact = false ) {
	$items = array();
	foreach ( multirent_unit_detail_labels() as $key => $label ) {
		$value = multirent_unit_detail( $post_id, $key );
		if ( $value ) {
			$items[] = array( 'label' => $label, 'value' => $value );
		}
	}

	if ( ! $items ) {
		return;
	}

	$items = $compact ? array_slice( $items, 0, 3 ) : $items;
	?>
	<ul class="detail-list">
		<?php foreach ( $items as $item ) : ?>
			<li><span><?php echo esc_html( $item['label'] ); ?></span><strong><?php echo esc_html( $item['value'] ); ?></strong></li>
		<?php endforeach; ?>
	</ul>
	<?php
}

function multirent_button_url( $url ) {
	return $url ? $url : '#';
}
